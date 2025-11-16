<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Invitation;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all invitations that don't have a code
        $invitations = Invitation::whereNull('code')->get();
        
        foreach ($invitations as $invitation) {
            $invitation->code = $this->generateUniqueCode();
            $invitation->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all codes to null (optional - you might want to keep them)
        Invitation::query()->update(['code' => null]);
    }

    /**
     * Generate a unique code for invitations
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (Invitation::where('code', $code)->exists());
        
        return $code;
    }
}; 
