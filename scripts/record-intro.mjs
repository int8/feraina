/**
 * Nagrywa scenę /studio/intro do public/videos/intro.{mp4,webm} + intro-poster.jpg.
 *
 *   php artisan serve            (w drugim terminalu)
 *   npm run build                (studio korzysta ze zbudowanych assetów)
 *   npm run record:intro
 *
 * Zmienne: STUDIO_URL (domyślnie http://127.0.0.1:8000/studio/intro), SECONDS (długość filmu; domyślnie tyle, ile trwa scena).
 */
import { chromium } from 'playwright';
import { execFileSync } from 'node:child_process';
import { mkdirSync, mkdtempSync, rmSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join } from 'node:path';

const url = process.env.STUDIO_URL || 'http://127.0.0.1:8000/studio/intro';
const seconds = Number(process.env.SECONDS || 0);
const out = 'public/videos';
const size = { width: 1920, height: 1080 };

mkdirSync(out, { recursive: true });
const tmp = mkdtempSync(join(tmpdir(), 'intro-'));

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

execFileSync('ffmpeg', [...common, '-vf', 'fps=30,format=yuv420p', '-c:v', 'libx264', '-preset', 'slow', '-crf', '19', '-movflags', '+faststart', `${out}/intro.mp4`], { stdio: 'inherit' });
execFileSync('ffmpeg', [...common, '-vf', 'fps=30', '-c:v', 'libvpx-vp9', '-b:v', '0', '-crf', '31', '-row-mt', '1', `${out}/intro.webm`], { stdio: 'inherit' });
execFileSync('ffmpeg', ['-y', '-ss', '1.6', '-i', `${out}/intro.mp4`, '-frames:v', '1', '-q:v', '3', `${out}/intro-poster.jpg`], { stdio: 'inherit' });

rmSync(tmp, { recursive: true, force: true });
console.log(`ok: ${out}/intro.mp4, ${out}/intro.webm, ${out}/intro-poster.jpg (lead ${lead}s)`);
