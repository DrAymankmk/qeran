from pathlib import Path

src_path = Path(__file__).parent.parent / 'resources/views/invitation/templates/partials/builder-wedding-source.html'
out_dir = src_path.parent
src = src_path.read_text(encoding='utf-8')

i0 = src.index('<style>') + len('<style>')
i1 = src.index('</style>')
b0 = src.index('<div class="wi-root">')
b1 = src.index('<script>')

(out_dir / 'builder-wedding-styles.blade.php').write_text(src[i0:i1], encoding='utf-8')
(out_dir / 'builder-wedding-body-raw.blade.php').write_text(src[b0:b1], encoding='utf-8')
print('extracted', len(src[i0:i1]), len(src[b0:b1]))
