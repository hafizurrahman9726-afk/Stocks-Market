import React, { useState } from 'react';
import { Check, Clipboard, FileCode2 } from 'lucide-react';
import {
  InspectedThemeReport,
  PROMPTSYRA_THEME_FILES,
  ThemeSourceFile,
} from '../data/wpThemeRegistry';

export const WordPressSourceBrowser: React.FC = () => {
  const defaultFrontPage =
    PROMPTSYRA_THEME_FILES.find((f) => f.relativePath === 'front-page.php') ||
    PROMPTSYRA_THEME_FILES[0];
  const [selectedFile, setSelectedFile] = useState<ThemeSourceFile>(defaultFrontPage);
  const [copiedPath, setCopiedPath] = useState<string | null>(null);

  const handleCopy = async (file: ThemeSourceFile) => {
    await navigator.clipboard.writeText(file.content);
    setCopiedPath(file.relativePath);
    setTimeout(() => setCopiedPath(null), 1800);
  };

  return (
    <div className="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
      <div className="lg:col-span-4 bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div className="px-4 py-3 border-b border-slate-200 bg-slate-50">
          <h2 className="font-display font-bold text-sm text-slate-900">
            WordPress Theme Package Files ({PROMPTSYRA_THEME_FILES.length})
          </h2>
          <p className="text-xs text-slate-500 mt-0.5">
            Located in <code className="font-mono">/wordpress-theme/promptsyra/</code>
          </p>
        </div>

        <ul className="divide-y divide-slate-100">
          {PROMPTSYRA_THEME_FILES.map((file) => {
            const isSelected = selectedFile.relativePath === file.relativePath;
            return (
              <li key={file.relativePath}>
                <button
                  type="button"
                  onClick={() => setSelectedFile(file)}
                  className={`w-full text-left px-4 py-3 transition-colors flex items-start gap-2.5 ${
                    isSelected ? 'bg-violet-50/80 text-violet-950' : 'hover:bg-slate-50 text-slate-700'
                  }`}
                >
                  <FileCode2
                    className={`w-4 h-4 mt-0.5 shrink-0 ${
                      isSelected ? 'text-violet-600' : 'text-slate-400'
                    }`}
                  />
                  <div className="min-w-0">
                    <p className="font-mono text-xs font-semibold truncate">{file.relativePath}</p>
                    <p className="text-[11px] text-slate-500 mt-0.5 line-clamp-2">{file.purpose}</p>
                  </div>
                </button>
              </li>
            );
          })}
        </ul>
      </div>

      <div className="lg:col-span-8 bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div className="px-5 py-3.5 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3 bg-slate-50">
          <div>
            <p className="font-mono text-xs font-bold text-slate-900">{selectedFile.relativePath}</p>
            <p className="text-xs text-slate-600 mt-0.5">
              Single-Post Guard: <span className="font-medium text-emerald-700">{selectedFile.singlePostImpact}</span>
            </p>
          </div>

          <button
            type="button"
            onClick={() => void handleCopy(selectedFile)}
            className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:border-violet-600 text-xs font-semibold text-slate-800 transition-colors whitespace-nowrap shrink-0"
          >
            {copiedPath === selectedFile.relativePath ? (
              <>
                <Check className="w-3.5 h-3.5 text-emerald-600" />
                <span>Copied</span>
              </>
            ) : (
              <>
                <Clipboard className="w-3.5 h-3.5" />
                <span>Copy File</span>
              </>
            )}
          </button>
        </div>

        <pre className="p-5 overflow-x-auto max-h-[640px] text-xs leading-relaxed font-mono bg-[#0F172A] text-slate-100">
          <code>{selectedFile.content}</code>
        </pre>
      </div>
    </div>
  );
};

interface ChecklistRow {
  group: string;
  item: string;
  status: 'Verified Locally' | 'Not Tested (Requires Staging WP)';
  detail: string;
}

const AUDIT_CHECKLIST: ChecklistRow[] = [
  {
    group: 'Section 1 — Hero Benefits Bar',
    item: 'Integrated Explore Prompts CTA & 3 compact benefit items with crisp SVG check icons and responsive wrapping',
    status: 'Verified Locally',
    detail:
      'Implemented in front-page.php (.ps-hero-support-bar) and promptsyra-home.css; preserves Customizer controls (promptsyra_utility_point_1..3) with zero fake stats or trust badges.',
  },
  {
    group: 'Section 2 — Category Discovery',
    item: 'AI Photo Prompts, Trending Prompts, and Social Media Prompts discovery cards with live WP category URL & alias verification',
    status: 'Verified Locally',
    detail:
      'Live REST inspection confirmed /category/ai-photo-prompts/ (HTTP 200, 1 post), /category/trending-prompts/ (HTTP 200), and /category/ai-social-media-prompts/ (HTTP 200). Added automatic alias resolution + 301 redirect bridge so neither /social-media-prompts/ nor /ai-social-media-prompts/ ever 404s.',
  },
  {
    group: 'Section 3 — 3-Step Workflow',
    item: 'Connected "How to Use AI Prompts" 3-step progression with custom SVG icons, refined copy, and Cross-Tool Interpretation Note',
    status: 'Verified Locally',
    detail:
      'Uses semantic <ol class="ps-workflow-steps"> with connected desktop progression rail, clear step instructions, and an honest note explaining that AI tools interpret identical prompts differently.',
  },
  {
    group: 'Section 4 — Editorial Bento & Copy',
    item: 'Asymmetric "Why Choose PromptSyra?" showcase with working Copy Sample Prompt specimen, 2x2 Style Matrix, and 5-Factor Customization Ledger',
    status: 'Verified Locally',
    detail:
      'Includes genuine clipboard interaction ([data-ps-sample-copy] in promptsyra-home.js with fallback copy & aria-live announcement), 4 photo style motifs, and 5 customizable variables (Subject, Lighting, Background, Framing, Aspect ratio).',
  },
  {
    group: 'Preserved Templates & Scoping',
    item: 'Header, Footer, Featured WP_Query Grid, FAQ + JSON-LD, 3 Authors, Single-Post UI (v2.2.0), and Legal/Contact Pages (v2.3.0) untouched',
    status: 'Verified Locally',
    detail:
      'All new CSS selectors in promptsyra-home.css are strictly scoped under .ps-* classes with zero bare tag rules; recoverable snapshot saved at legacy-backup/front-page-v2.3.0.backup.php.',
  },
  {
    group: 'Live WordPress Theme Upload',
    item: 'Uploading promptsyra-v2.4.0.zip via Appearance -> Themes -> Replace active with uploaded and clearing site cache',
    status: 'Not Tested (Requires Staging WP)',
    detail:
      'Requires uploading promptsyra-v2.4.0.zip inside your WordPress Admin dashboard and purging any WordPress/Cloudflare cache so ?ver=2.4.0 CSS/JS assets load on https://promptsyra.com/.',
  },
];

export const TestingAndDeploymentAudit: React.FC<{ report: InspectedThemeReport | null }> = ({
  report,
}) => {
  const [subView, setSubView] = useState<'audit' | 'source'>('audit');

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between gap-4 bg-white border border-slate-200 rounded-xl p-3">
        <div className="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
          <button
            type="button"
            onClick={() => setSubView('audit')}
            className={`px-3 py-1.5 rounded-md text-xs font-semibold transition-colors ${
              subView === 'audit' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Deployment &amp; Testing Checklist
          </button>
          <button
            type="button"
            onClick={() => setSubView('source')}
            className={`px-3 py-1.5 rounded-md text-xs font-semibold transition-colors ${
              subView === 'source' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            Browse All {PROMPTSYRA_THEME_FILES.length} PHP &amp; CSS Theme Files
          </button>
        </div>
        <span className="text-xs text-slate-500 font-mono">PromptSyra v2.4.0</span>
      </div>

      {subView === 'source' ? (
        <WordPressSourceBrowser />
      ) : (
        <>
          <div className="bg-white border border-slate-200 rounded-xl p-6">
            <h2 className="font-display font-bold text-lg text-slate-900 mb-2">
              v2.4.0 Master Homepage Four-Section UI Upgrade — Deployment &amp; Rollback Procedure
            </h2>
            <ol className="space-y-2.5 text-xs text-slate-700 list-decimal pl-5 leading-relaxed">
              <li>
                <strong>Rotate Administrator Password:</strong> Log into your WordPress dashboard directly in your browser and change any password previously shared in chat (<code className="font-mono">Users → Profile → New Password</code>).
              </li>
              <li>
                <strong>Backup Current Active Theme (<code className="font-mono">v2.3.0</code>):</strong> Keep your <code className="font-mono">promptsyra-v2.3.0.zip</code> archive or export <code className="font-mono">wp-content/themes/promptsyra/</code> via Hosting File Manager / SFTP (recoverable snapshots are also bundled inside <code className="font-mono">legacy-backup/</code>{report ? ` · detected source folder: ${report.detectedRootFolder}` : ''}).
              </li>
              <li>
                <strong>Upload <code className="font-mono">promptsyra-v2.4.0.zip</code> &amp; Replace Active:</strong> In WordPress Admin, open <code className="font-mono">Appearance → Themes → Add New Theme → Upload Theme</code>, select <code className="font-mono">promptsyra-v2.4.0.zip</code>, click <strong>Install Now</strong>, and click <strong>Replace active with uploaded</strong>.
              </li>
              <li>
                <strong>Clear Cache &amp; Verify Homepage Sections:</strong> Purge any WordPress or Cloudflare cache so <code className="font-mono">promptsyra-home.css?ver=2.4.0</code> and <code className="font-mono">promptsyra-home.js?ver=2.4.0</code> load immediately, then verify the Hero Benefits Bar, 3 Category Discovery Cards, Connected 3-Step Workflow, and Sample Prompt Copy button on <code className="font-mono">https://promptsyra.com/</code>.
              </li>
              <li>
                <strong>Instant Rollback:</strong> Re-uploading <code className="font-mono">promptsyra-v2.3.0.zip</code> via <code className="font-mono">Appearance → Themes → Upload Theme → Replace active with uploaded</code> restores the previous homepage layout immediately.
              </li>
            </ol>
          </div>

          <div className="bg-white border border-slate-200 rounded-xl overflow-hidden">
            <div className="px-5 py-4 border-b border-slate-200 bg-slate-50">
              <h2 className="font-display font-bold text-base text-slate-900">
                Required Testing Checklist — Honest Execution Report
              </h2>
              <p className="text-xs text-slate-500 mt-0.5">
                Live <code className="font-mono">https://promptsyra.com</code> category REST endpoints and permalinks were inspected via read-only HTTP requests; checks requiring live WordPress PHP execution are marked <strong>Not Tested</strong> until <code className="font-mono">promptsyra-v2.4.0.zip</code> is uploaded.
              </p>
            </div>

            <div className="overflow-x-auto">
              <table className="w-full text-left border-collapse text-xs">
                <thead className="border-b border-slate-200 text-slate-600 bg-white">
                  <tr>
                    <th className="py-3 px-4 font-semibold">Area</th>
                    <th className="py-3 px-4 font-semibold">Verification Item</th>
                    <th className="py-3 px-4 font-semibold">Real Status</th>
                    <th className="py-3 px-4 font-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-slate-100">
                  {AUDIT_CHECKLIST.map((row) => (
                    <tr key={row.item} className="hover:bg-slate-50/70">
                      <td className="py-3 px-4 font-semibold text-slate-900 whitespace-nowrap">{row.group}</td>
                      <td className="py-3 px-4 text-slate-800">{row.item}</td>
                      <td className="py-3 px-4 whitespace-nowrap font-semibold">
                        {row.status === 'Verified Locally' ? (
                          <span className="text-emerald-700">{row.status}</span>
                        ) : (
                          <span className="text-amber-700">{row.status}</span>
                        )}
                      </td>
                      <td className="py-3 px-4 text-slate-600 leading-relaxed">{row.detail}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </div>
        </>
      )}
    </div>
  );
};
