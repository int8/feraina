/**
 * Kopiuje logotypy z pakietu simple-icons do resources/svg/brands (wg content/brands.json, pole "icon"),
 * żeby produkcja nie potrzebowała node_modules. Uruchom po zmianie listy marek: npm run sync:brands
 */
import { readFileSync, copyFileSync, existsSync, mkdirSync } from 'node:fs';

const brands = JSON.parse(readFileSync('content/brands.json', 'utf8'));
mkdirSync('resources/svg/brands', { recursive: true });
let n = 0;
for (const [id, b] of Object.entries(brands)) {
    if (!b || typeof b !== 'object' || !b.icon) continue;
    const src = `node_modules/simple-icons/icons/${b.icon}.svg`;
    if (!existsSync(src)) { console.warn(`brak ikony ${b.icon} (${id})`); continue; }
    copyFileSync(src, `resources/svg/brands/${b.icon}.svg`);
    n++;
}
console.log(`ok: ${n} logotypów`);
