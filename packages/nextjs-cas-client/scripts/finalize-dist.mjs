/**
 * Post-build step: stamp each dist subtree with the package.json "type"
 * marker that tells Node how to interpret its .js files. Without these,
 * both trees would inherit the root package type and one of the two
 * builds would be loaded with the wrong module semantics.
 */
import { writeFileSync } from 'node:fs';

writeFileSync('dist/esm/package.json', JSON.stringify({ type: 'module' }, null, 2) + '\n');
writeFileSync('dist/cjs/package.json', JSON.stringify({ type: 'commonjs' }, null, 2) + '\n');
