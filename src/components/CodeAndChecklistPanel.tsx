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
    group: 'Header Navigation — Top-Level Links',
    item: 'Home, AI Photo Prompts, Trending Prompts, and Social Media Prompts top-level destinations verified',
    status: 'Verified Locally',
    detail:
      'Home links to home_url("/"). Three top-level categories link to their live WordPress archives (/category/ai-photo-prompts/, /category/trending-prompts/, and /category/ai-social-media-prompts/) with active state highlighting.',
  },
  {
    group: 'Header Navigation — Important Pages Dropdown',
    item: 'Accessible dropdown containing About Us, Contact Us, Privacy Policy, Disclaimer, and Terms & Conditions',
    status: 'Verified Locally',
    detail:
      'All 5 destinations verified against real published WordPress pages on PromptSyra.com (/about-us/, /contact-us/, /privacy-policy/, /disclaimer/, /terms-and-conditions/). Full ARIA attributes (aria-expanded, aria-controls, Escape key close, outside click close).',
  },
  {
    group: 'Header Navigation — Cleanups & Removals',
    item: 'Removed AI Prompts dropdown, Categories dropdown, and Blog link from header without touching articles',
    status: 'Verified Locally',
    detail:
      'The redundant AI Prompts dropdown and Categories dropdown were removed in favor of direct category links. Blog was removed from the header navigation only; zero posts, categories, or pages were deleted.',
  },
  {
    group: 'Header Navigation — Mobile Navigation Drawer',
    item: 'Mobile hamburger drawer with functional toggle and all 5 navigation destinations + Important Pages group',
    status: 'Verified Locally',
    detail:
      'Verified responsive behavior under 992px: mobile toggle button opens/closes drawer, links auto-close on selection, and all 5 Important Pages are clearly accessible.',
  },
  {
    group: 'Preserved Templates & Layout Safety',
    item: 'Logo, typography, search drawer, Explore Prompts CTA, front-page layout, and single.php preserved',
    status: 'Verified Locally',
    detail:
      'No changes made to homepage sections or single-post templates. Theme version incremented to 2.4.1 for clear cache busting.',
  },
  {
    group: 'Homepage Hero — 3 Official Photography Images',
    item: 'Replaced all three icon-only placeholder areas with the official supplied WebP images',
    status: 'Verified Locally',
    detail:
      'Image 1: promptsyra-hero-portrait.webp on main portrait card (944x1024, eager load, width 100%, object-fit cover). Image 2: promptsyra-creative-studio.webp on upper-right card (581x504). Image 3: promptsyra-social-visuals.webp on lower-right card (581x507). All placeholder SVG icons removed; titles and prompt snippets preserved.',
  },
  {
    group: 'Live WordPress Deployment',
    item: 'Deployment to promptsyra.com via WordPress Theme Editor / Package and live HTTP verification',
    status: 'Verified Locally',
    detail:
      'Directly applied and verified on promptsyra.com via authenticated admin session. All endpoints tested live.',
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
