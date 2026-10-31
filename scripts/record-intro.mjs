/**
 * Nagrywa scenę /studio/<nazwa> do public/videos/<nazwa>.{mp4,webm} + <nazwa>-poster.jpg.
 *
 *   php artisan serve            (w drugim terminalu)
 *   npm run build                (studio korzysta ze zbudowanych assetów)
 *   npm run record:intro         (scena intro → zakładka „Web” w hero)
 *   npm run record:whatsapp      (scena whatsapp → zakładka „WhatsApp”)
 *   npm run record:all           (obie sceny, poziomo 1920×1080 i pionowo 1080×1920 → <nazwa>-mobile.* na telefony)
 *
 * Zmienne: STUDIO_URL (domyślnie http://127.0.0.1:8000/studio/<nazwa>), SECONDS (długość filmu; domyślnie tyle, ile trwa scena),
 * POSTER_AT (sekunda plakatu, domyślnie 1.6).
 */
import { chromium } from 'playwright';
import { execFileSync } from 'node:child_process';
import { mkdirSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const scene = (process.argv[2] || 'intro').replace(/[^a-z0-9-]/g, '');
const isPortrait = process.argv[3] === 'portrait';
const name = isPortrait ? `${scene}-mobile` : scene;
const url = process.env.STUDIO_URL || `http://127.0.0.1:8000/studio/${scene}${isPortrait ? '?portrait=1' : ''}`;
const posterAt = process.env.POSTER_AT || '1.6';
const seconds = Number(process.env.SECONDS || 0);
const out = 'public/videos';
const size = isPortrait ? { width: 1080, height: 1920 } : { width: 1920, height: 1080 };

mkdirSync(out, { recursive: true });
const tmp = mkdtempSync(join(tmpdir(), `${name}-`));

const browser = await chromium.launch();
const context = await browser.newContext({ viewport: size, deviceScaleFactor: 1, recordVideo: { dir: tmp, size } });
const page = await context.newPage();
const recordingStarted = Date.now();

await page.goto(url, { waitUntil: 'networkidle' });
await page.evaluate(() => document.fonts.ready);
await page.waitForTimeout(400);

const sceneStarted = Date.now();
const timeline = await page.evaluate(() => window.__studioStart());
console.log('timeline', timeline);
// Długość: ze sceny (timeline.total), chyba że podano SECONDS.
const length = process.env.SECONDS ? seconds : Math.ceil(timeline.total / 1000);
await page.waitForTimeout(length * 1000 + 300);

const video = page.video();
await context.close();
const webm = await video.path();
await browser.close();

// Ucinamy czas przed startem sceny (ładowanie strony).
const lead = ((sceneStarted - recordingStarted) / 1000).toFixed(3);
const common = ['-y', '-ss', lead, '-i', webm, '-t', String(length), '-an'];

execFileSync('ffmpeg', [...common, '-vf', 'fps=30,format=yuv420p', '-c:v', 'libx264', '-preset', 'slow', '-crf', '19', '-movflags', '+faststart', `${out}/${name}.mp4`], { stdio: 'inherit' });
execFileSync('ffmpeg', [...common, '-vf', 'fps=30', '-c:v', 'libvpx-vp9', '-b:v', '0', '-crf', '31', '-row-mt', '1', `${out}/${name}.webm`], { stdio: 'inherit' });
execFileSync('ffmpeg', ['-y', '-ss', posterAt, '-i', `${out}/${name}.mp4`, '-frames:v', '1', '-q:v', '3', `${out}/${name}-poster.jpg`], { stdio: 'inherit' });

rmSync(tmp, { recursive: true, force: true });
console.log(`ok: ${out}/${name}.mp4, ${out}/${name}.webm, ${out}/${name}-poster.jpg (lead ${lead}s)`);
