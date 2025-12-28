<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;

class TranslationController extends Controller
{
    /**
     * Display a listing of all translation keys grouped by file and locale
     */
    public function index(Request $request)
    {
        $locale = $request->get('locale', 'ar');
        $file = $request->get('file', null);
        
        // Get all translations as a flat array for DataTables
        $allTranslations = $this->getAllTranslationsFlat($locale, $file);
        $availableLocales = $this->getAvailableLocales();
        $availableFiles = $this->getAvailableFiles();
        
        return view('admin.translations.index', compact('allTranslations', 'locale', 'file', 'availableLocales', 'availableFiles'));
    }

    /**
     * Show the form for editing a translation
     */
    public function edit(Request $request, $locale, $file, $key)
    {
        $key = urldecode($key);
        $keyPath = $key; // Keep dot notation for data_get
        $translationPath = lang_path("{$locale}/{$file}.php");
        
        if (!File::exists($translationPath)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Translation file not found.'], 404);
            }
            return redirect()->route('admin.translations.index')
                ->with('error', 'Translation file not found.');
        }
        
        $translations = include $translationPath;
        $value = data_get($translations, $keyPath, '');
        
        $availableLocales = $this->getAvailableLocales();
        
        if ($request->ajax()) {
            return response()->json([
                'locale' => $locale,
                'file' => $file,
                'key' => $key,
                'keyPath' => $keyPath,
                'value' => $value,
            ]);
        }
        
        return view('admin.translations.edit', compact('locale', 'file', 'key', 'keyPath', 'value', 'availableLocales'));
    }

    /**
     * Update a translation value
     */
    public function update(Request $request, $locale, $file, $key)
    {
        $request->validate([
            'value' => 'required|string',
        ]);
        
        $key = urldecode($key);
        $translationPath = lang_path("{$locale}/{$file}.php");
        
        if (!File::exists($translationPath)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Translation file not found.'], 404);
            }
            return redirect()->route('admin.translations.index')
                ->with('error', 'Translation file not found.');
        }
        
        $translations = include $translationPath;
        $keyPath = $key; // Use dot notation for data_set
        
        // Update the value using dot notation
        data_set($translations, $keyPath, $request->value);
        
        // Write back to file
        $this->writeTranslationFile($translationPath, $translations);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Translation updated successfully.'
            ]);
        }
        
        return redirect()->route('admin.translations.index', ['locale' => $locale, 'file' => $file])
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Show the form for creating a new translation key
     */
    public function create(Request $request)
    {
        $locale = $request->get('locale', 'ar');
        $file = $request->get('file', 'cms');
        
        $availableLocales = $this->getAvailableLocales();
        $availableFiles = $this->getAvailableFiles();
        
        if ($request->ajax()) {
            return response()->json([
                'locale' => $locale,
                'file' => $file,
                'availableLocales' => $availableLocales,
                'availableFiles' => $availableFiles,
            ]);
        }
        
        return view('admin.translations.create', compact('locale', 'file', 'availableLocales', 'availableFiles'));
    }

    /**
     * Store a new translation key
     */
    public function store(Request $request)
    {
        $request->validate([
            'locale' => 'required|string',
            'file' => 'required|string',
            'key' => 'required|string|regex:/^[a-z0-9_-]+(\.[a-z0-9_-]+)*$/i',
            'value' => 'required|string',
        ]);
        
        $locale = $request->locale;
        $file = $request->file;
        $key = $request->key;
        $value = $request->value;
        
        $translationPath = lang_path("{$locale}/{$file}.php");
        
        // Create directory if it doesn't exist
        if (!File::exists(dirname($translationPath))) {
            File::makeDirectory(dirname($translationPath), 0755, true);
        }
        
        // Load existing translations or create new array
        $translations = File::exists($translationPath) ? include $translationPath : [];
        
        // Check if key already exists
        $keyPath = $key; // Use dot notation
        if (data_get($translations, $keyPath) !== null) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Translation key already exists.'], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', 'Translation key already exists.');
        }
        
        // Add new key
        data_set($translations, $keyPath, $value);
        
        // Write back to file
        $this->writeTranslationFile($translationPath, $translations);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Translation key added successfully.'
            ]);
        }
        
        return redirect()->route('admin.translations.index', ['locale' => $locale, 'file' => $file])
            ->with('success', 'Translation key added successfully.');
    }

    /**
     * Get all translations for a locale and optionally filter by file
     */
    private function getAllTranslations($locale, $file = null)
    {
        $translations = [];
        $langPath = lang_path($locale);
        
        if (!File::exists($langPath)) {
            return $translations;
        }
        
        $files = File::files($langPath);
        
        foreach ($files as $filePath) {
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
            
            // Filter by file if specified
            if ($file && $fileName !== $file) {
                continue;
            }
            
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                $fileTranslations = include $filePath;
                $translations[$fileName] = $this->flattenTranslations($fileTranslations, '', $fileName);
            }
        }
        
        return $translations;
    }

    /**
     * Get all translations as a flat array for DataTables
     */
    private function getAllTranslationsFlat($locale, $file = null)
    {
        $allTranslations = [];
        $langPath = lang_path($locale);
        
        if (!File::exists($langPath)) {
            return $allTranslations;
        }
        
        $files = File::files($langPath);
        
        foreach ($files as $filePath) {
            $fileName = pathinfo($filePath, PATHINFO_FILENAME);
            
            // Filter by file if specified
            if ($file && $fileName !== $file) {
                continue;
            }
            
            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                $fileTranslations = include $filePath;
                $flattened = $this->flattenTranslations($fileTranslations, '', $fileName);
                
                // Add file name to each translation
                foreach ($flattened as $key => $translation) {
                    $allTranslations[] = [
                        'key' => $translation['key'],
                        'value' => $translation['value'],
                        'file' => $fileName,
                    ];
                }
            }
        }
        
        return $allTranslations;
    }

    /**
     * Flatten nested translation array to dot notation
     */
    private function flattenTranslations($array, $prefix = '', $file = '')
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenTranslations($value, $newKey, $file));
            } else {
                $result[$newKey] = [
                    'key' => $newKey,
                    'value' => $value,
                    'file' => $file,
                ];
            }
        }
        
        return $result;
    }

    /**
     * Get available locales
     */
    private function getAvailableLocales()
    {
        $locales = [];
        $langPath = lang_path();
        
        if (File::exists($langPath)) {
            $directories = File::directories($langPath);
            foreach ($directories as $dir) {
                $locales[] = basename($dir);
            }
        }
        
        return $locales;
    }

    /**
     * Get available translation files
     */
    private function getAvailableFiles()
    {
        $files = [];
        $langPath = lang_path();
        
        if (File::exists($langPath)) {
            $directories = File::directories($langPath);
            foreach ($directories as $dir) {
                $locale = basename($dir);
                $phpFiles = File::files($dir);
                foreach ($phpFiles as $file) {
                    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                        $fileName = pathinfo($file, PATHINFO_FILENAME);
                        if (!in_array($fileName, $files)) {
                            $files[] = $fileName;
                        }
                    }
                }
            }
        }
        
        sort($files);
        return $files;
    }

    /**
     * Write translations array to PHP file
     */
    private function writeTranslationFile($path, $translations)
    {
        $content = "<?php\n\nreturn [\n";
        $content .= $this->arrayToPhpString($translations, 1);
        $content .= "];\n";
        
        File::put($path, $content);
    }

    /**
     * Convert array to PHP string representation
     */
    private function arrayToPhpString($array, $indent = 0)
    {
        $spaces = str_repeat('    ', $indent);
        $result = '';
        
        foreach ($array as $key => $value) {
            $keyString = is_numeric($key) ? $key : "'" . addslashes($key) . "'";
            
            if (is_array($value)) {
                $result .= "{$spaces}{$keyString} => [\n";
                $result .= $this->arrayToPhpString($value, $indent + 1);
                $result .= "{$spaces}],\n";
            } else {
                // Use heredoc or nowdoc for strings with newlines or special characters
                if (is_string($value) && (strpos($value, "\n") !== false || strpos($value, "'") !== false || strpos($value, "\\") !== false)) {
                    // Escape properly for single quotes
                    $valueString = "'" . str_replace(["\\", "'"], ["\\\\", "\\'"], $value) . "'";
                } else {
                    $valueString = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
                }
                $result .= "{$spaces}{$keyString} => {$valueString},\n";
            }
        }
        
        return $result;
    }
}

