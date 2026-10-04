// Round-trips every stored rich-text value through Tiptap with oxid's editor
// config and compares what a browser would show before and after.
// Adapted from luvo's tool. Needs happy-dom, @tiptap/html, @tiptap/core,
// @tiptap/starter-kit, @tiptap/extension-link, @tiptap/extension-superscript.
//
//   node tiptap-roundtrip.mjs content.json
//
// content.json: [{ key, html }, ...] — export it from the DB, keep it out of git.
import fs from 'node:fs';
import { Window } from 'happy-dom';
import { generateJSON, generateHTML } from '@tiptap/html';
import { Mark, Node } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Superscript from '@tiptap/extension-superscript';

// --- same as resources/js/backend/components/global/editor ---
const NoWrap = Mark.create({
  name: 'noWrap',
  parseHTML() { return [{ tag: 'span', getAttrs: el => el.style.whiteSpace === 'nowrap' && null }]; },
  renderHTML() { return ['span', { style: 'white-space: nowrap;' }, 0]; },
});
const Div = Node.create({
  name: 'div', group: 'block', content: 'inline*',
  parseHTML() { return [{ tag: 'div' }]; },
  renderHTML() { return ['div', 0]; },
});
const extensions = [
  StarterKit.configure({
    heading: { levels: [1, 2, 3] },
    blockquote: false, code: false, codeBlock: false, horizontalRule: false,
    strike: false, underline: false, link: false,
  }),
  Link.configure({ openOnClick: false, autolink: false, HTMLAttributes: { target: null, rel: null } }),
  Superscript,
  NoWrap,
  Div,
];
// ------------------------------------------------------------

const window = new Window();
const parse = html => { const d = window.document.createElement('div'); d.innerHTML = html; return d; };

function facts(html) {
  const root = parse(html);
  root.querySelectorAll('style, xml, script, title, meta, link').forEach(e => e.remove());
  const walker = window.document.createTreeWalker(root, 128);
  const comments = []; while (walker.nextNode()) comments.push(walker.currentNode);
  comments.forEach(c => c.remove());
  const brs = root.querySelectorAll('br').length;
  root.querySelectorAll('br').forEach(br => br.replaceWith(window.document.createTextNode(' ')));
  root.querySelectorAll('p, li, h1, h2, h3, h4, div, ul, ol, td, tr, figure').forEach(el => el.append(window.document.createTextNode(' ')));
  const text = (root.textContent || '').replace(/ /g, ' ').replace(/\s+/g, ' ').trim();
  return {
    text,
    links: [...root.querySelectorAll('a[href]')].map(a => a.getAttribute('href')),
    targets: [...root.querySelectorAll('a[href]')].map(a => a.getAttribute('target') || ''),
    h: ['h1', 'h2', 'h3', 'h4'].map(h => root.querySelectorAll(h).length).join('/'),
    br: brs,
    li: root.querySelectorAll('li').length,
    bold: root.querySelectorAll('strong, b').length,
    sup: root.querySelectorAll('sup').length,
    nowrap: [...root.querySelectorAll('span')].filter(s => s.style.whiteSpace === 'nowrap').length,
    imgs: root.querySelectorAll('img').length,
  };
}

const items = JSON.parse(fs.readFileSync(process.argv[2]));
const problems = [];
let bytesBefore = 0, bytesAfter = 0;

for (const { key, html } of items) {
  const out = generateHTML(generateJSON(html, extensions), extensions);
  bytesBefore += html.length; bytesAfter += out.length;
  const a = facts(html), b = facts(out);
  const diffs = [];
  if (a.text !== b.text) diffs.push(`text (${a.text.length}→${b.text.length})`);
  if (JSON.stringify(a.links) !== JSON.stringify(b.links)) diffs.push(`links ${JSON.stringify(a.links)} → ${JSON.stringify(b.links)}`);
  if (JSON.stringify(a.targets) !== JSON.stringify(b.targets)) diffs.push(`targets ${JSON.stringify(a.targets)} → ${JSON.stringify(b.targets)}`);
  for (const k of ['h', 'br', 'li', 'bold', 'sup', 'nowrap', 'imgs']) if (a[k] !== b[k]) diffs.push(`${k} ${a[k]}→${b[k]}`);
  if (diffs.length) problems.push({ key, diffs, a, b, html, out });
}

console.log(`${items.length} values, ${problems.length} with visible differences; ${bytesBefore} → ${bytesAfter} bytes`);
for (const p of problems) {
  console.log(`\n== ${p.key}: ${p.diffs.join('; ')}`);
  if (p.a.text !== p.b.text) {
    let i = 0; while (i < p.a.text.length && p.a.text[i] === p.b.text[i]) i++;
    console.log('   before: …' + p.a.text.slice(Math.max(0, i - 40), i + 80));
    console.log('   after : …' + p.b.text.slice(Math.max(0, i - 40), i + 80));
  }
  if (process.env.VERBOSE) console.log('   html  : ' + p.html.slice(0, 400) + '\n   out   : ' + p.out.slice(0, 400));
}
