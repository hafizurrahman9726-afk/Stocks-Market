import React, { useRef, useState } from 'react';
import {
  Archive,
  CheckCircle2,
  Download,
  FileArchive,
  Info,
  Lock,
  Plug,
  ShieldCheck,
  Upload,
} from 'lucide-react';
import {
  buildChildThemeZipBlob,
  buildMergedClonedThemeZipBlob,
  buildStandaloneThemeZipBlob,
  buildWordPressPluginZipBlob,
  InspectedThemeReport,
  inspectUploadedThemeZip,
  triggerBlobDownload,
} from '../data/wpThemeRegistry';

interface ThemeZipInspectorProps {
  report: InspectedThemeReport | null;
  onReportGenerated: (report: InspectedThemeReport) => void;
}

export const ThemeZipInspector: React.FC<ThemeZipInspectorProps> = ({
  report,
  onReportGenerated,
}) => {
  const fileInputRef = useRef<HTMLInputElement | null>(null);
  const [isInspecting, setIsInspecting] = useState(false);
  const [isExporting, setIsExporting] = useState(false);
  const [errorMsg, setErrorMsg] = useState<string | null>(null);

  const handleZipSelection = async (file: File | undefined) => {
    if (!file) return;
    setErrorMsg(null);
    if (!file.name.toLowerCase().endsWith('.zip')) {
      setErrorMsg('Please select a valid WordPress theme .zip archive.');
      return;
    }

    setIsInspecting(true);
    try {
      const inspected = await inspectUploadedThemeZip(file);
      onReportGenerated(inspected);
    } catch (err) {
      setErrorMsg(
        err instanceof Error
          ? `Unable to parse ZIP archive: ${err.message}`
          : 'Unable to read the selected ZIP file.'
      );
    } finally {
      setIsInspecting(false);
    }
  };

  const handleDownloadStandaloneTheme = async () => {
    setIsExporting(true);
    try {
      const blob = await buildStandaloneThemeZipBlob();
      triggerBlobDownload(blob, 'promptsyra-v2.4.0.zip');
    } catch (err) {
      setErrorMsg(err instanceof Error ? err.message : 'Failed to build Standalone Theme ZIP.');
    } finally {
      setIsExporting(false);
    }
  };

  const handleDownloadPluginInstaller = async () => {
    setIsExporting(true);
    try {
      const blob = await buildWordPressPluginZipBlob();
      triggerBlobDownload(blob, 'promptsyra-rebrand-plugin-v2.4.0.zip');
    } catch (err) {
      setErrorMsg(err instanceof Error ? err.message : 'Failed to build WordPress Plugin ZIP.');
    } finally {
      setIsExporting(false);
    }
  };

  const handleDownloadOriginalBackup = () => {
    if (!report) return;
    const backupBlob = new Blob([report.rawArrayBuffer], { type: 'application/zip' });
    const safeBase = report.detectedRootFolder || 'aipromptzyra';
    triggerBlobDownload(backupBlob, `${safeBase}-v${report.detectedVersion}-verified-backup.zip`);
  };

  const handleDownloadVerifiedChildTheme = async () => {
    if (!report) return;
    setIsExporting(true);
    try {
      const blob = await buildChildThemeZipBlob(report.detectedRootFolder);
      triggerBlobDownload(blob, 'promptsyra-child-v2.4.0.zip');
    } catch (err) {
      setErrorMsg(err instanceof Error ? err.message : 'Failed to build Child Theme ZIP.');
    } finally {
      setIsExporting(false);
    }
  };

  const handleDownloadMergedClone = async () => {
    if (!report) return;
    setIsExporting(true);
    try {
      const mergedBlob = await buildMergedClonedThemeZipBlob(report);
      triggerBlobDownload(mergedBlob, 'promptsyra-merged-theme-v2.4.0.zip');
    } catch (err) {
      setErrorMsg(err instanceof Error ? err.message : 'Failed to generate merged theme ZIP.');
    } finally {
      setIsExporting(false);
    }
  };

  return (
    <div className="space-y-6">
      {/* v2.4.0 Upgrade Banner: Master Homepage Four-Section UI Upgrade */}
      <div className="bg-violet-50 border border-violet-200 rounded-xl p-5 flex items-start gap-3.5">
        <Info className="w-5 h-5 text-violet-700 shrink-0 mt-0.5" />
        <div className="space-y-1.5 text-xs text-slate-800 leading-relaxed">
          <h2 className="font-display font-bold text-sm text-slate-900">
            Upgrade Active Theme to v2.4.0 — Master Four-Section Homepage Editorial Redesign
          </h2>
          <p>
            Package <code className="font-mono font-semibold">promptsyra-v2.4.0.zip</code> upgrades the four homepage sections into distinct editorial layouts—the <strong>Integrated Hero Benefits Bar</strong>, <strong>3 Primary Category Discovery Cards</strong> (with automatic live WordPress alias resolution for <code className="font-mono">/category/ai-social-media-prompts/</code> and <code className="font-mono">/category/social-media-prompts/</code>), <strong>Connected 3-Step AI Prompt Workflow</strong>, and <strong>Asymmetric Why Choose PromptSyra? Showcase</strong> (with a working <strong>Copy Sample Prompt</strong> specimen)—while preserving all single-post, author, contact, and legal templates:
          </p>
          <ol className="list-decimal pl-4 space-y-1 text-slate-700">
            <li>
              Click <strong>Download Standalone Theme ZIP (promptsyra-v2.4.0.zip)</strong> below (or preview the upgraded UI live in the <strong>Homepage</strong> tab above).
            </li>
            <li>
              In WordPress Admin, open <code className="font-mono font-semibold">Appearance → Themes → Add New Theme → Upload Theme</code>, select <code className="font-mono font-semibold">promptsyra-v2.4.0.zip</code>, click <strong>Install Now</strong>, and click <strong>Replace active with uploaded</strong>.
            </li>
            <li>
              Purge any WordPress or Cloudflare cache so <code className="font-mono">promptsyra-home.css?ver=2.4.0</code> and <code className="font-mono">promptsyra-home.js?ver=2.4.0</code> serve immediately on <code className="font-mono">https://promptsyra.com/</code>.
            </li>
          </ol>
        </div>
      </div>

      {/* Primary Standalone Theme Card + Alternative Zero-Touch Plugin Card */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        <div className="lg:col-span-7 bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-between">
          <div>
            <p className="font-display text-xs font-bold text-violet-600 mb-1">
              Recommended for Appearance → Themes → Upload Theme
            </p>
            <h3 className="font-display font-bold text-lg text-slate-900 mb-2">
              Standalone WordPress Theme (<code className="font-mono text-sm">promptsyra-v2.4.0.zip</code>)
            </h3>
            <p className="text-xs text-slate-600 leading-relaxed mb-5">
              100% self-contained WordPress theme with <strong>no <code className="font-mono">Template:</code> parent dependency</strong>. Includes the <code className="font-mono">v2.4.0</code> four-section homepage redesign, <code className="font-mono">v2.2.0</code> single-post layout &amp; comments, and <code className="font-mono">v2.3.0</code> 3-author profiles, About Us, Contact Us (<code className="font-mono">contact@promptsyra.com</code>), Privacy Policy, Disclaimer, and Terms &amp; Conditions.
            </p>

            <button
              type="button"
              disabled={isExporting}
              onClick={() => void handleDownloadStandaloneTheme()}
              className="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold transition-colors whitespace-nowrap"
            >
              <Download className="w-4 h-4" />
              <span>Download Standalone Theme ZIP (promptsyra-v2.4.0.zip)</span>
            </button>
          </div>

          <p className="mt-4 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
            Installs into <code className="font-mono">wp-content/themes/promptsyra/</code> alongside your existing theme so your original theme is never overwritten.
          </p>
        </div>

        <div className="lg:col-span-5 bg-white border border-slate-200 rounded-xl p-6 flex flex-col justify-between">
          <div>
            <p className="font-display text-xs font-bold text-emerald-700 mb-1">
              Alternative · Zero Single-Post Change Mode
            </p>
            <h3 className="font-display font-bold text-base text-slate-900 mb-2">
              Keep Original Theme Active + Use Plugin Mode
            </h3>
            <p className="text-xs text-slate-600 leading-relaxed mb-4">
              Want to keep your exact <code className="font-mono">AIPromptZyra 1.1.4</code> single-post template active while replacing only the homepage and archives? Upload this file under{' '}
              <code className="font-mono font-semibold">Plugins → Add New Plugin → Upload Plugin</code>:
            </p>

            <button
              type="button"
              disabled={isExporting}
              onClick={() => void handleDownloadPluginInstaller()}
              className="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 bg-white hover:border-emerald-600 text-slate-900 text-xs font-semibold transition-colors whitespace-nowrap"
            >
              <Plug className="w-4 h-4 text-emerald-700" />
              <span>Download Plugin ZIP (For Plugins → Add New)</span>
            </button>
          </div>

          <p className="mt-4 text-[11px] text-slate-500">
            Security note: AI Studio cannot log into external <code className="font-mono">wp-admin</code> URLs—please rotate the password shared earlier in <code className="font-mono">Users → Profile</code>.
          </p>
        </div>
      </div>

      {/* Optional: Upload Original AIPromptZyra ZIP to Detect Exact Folder Slug & Merge */}
      <div className="bg-white border border-slate-200 rounded-xl p-6">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
          <div className="lg:col-span-7">
            <p className="font-display text-xs font-bold text-violet-600 mb-1">
              Optional · Auto-Detect Exact Parent Folder Slug
            </p>
            <h3 className="font-display font-bold text-base text-slate-900 mb-1.5">
              Drop Your Original <code className="font-mono text-sm">AIPromptZyra</code> ZIP to Build a Matched Child or Merged Theme
            </h3>
            <p className="text-xs text-slate-600 leading-relaxed">
              Automatically reads the exact case-sensitive folder name from your original theme ZIP so Child Theme or Merged Theme packages match 100%.
            </p>
          </div>

          <div className="lg:col-span-5">
            <div
              onDragOver={(e) => e.preventDefault()}
              onDrop={(e) => {
                e.preventDefault();
                void handleZipSelection(e.dataTransfer.files?.[0]);
              }}
              className="border-2 border-dashed border-slate-300 hover:border-violet-500 rounded-xl p-4 text-center bg-slate-50/70 transition-colors"
            >
              <input
                ref={fileInputRef}
                type="file"
                accept=".zip,application/zip"
                onChange={(e) => void handleZipSelection(e.target.files?.[0])}
                className="hidden"
              />
              <button
                type="button"
                disabled={isInspecting}
                onClick={() => fileInputRef.current?.click()}
                className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold transition-colors whitespace-nowrap"
              >
                <FileArchive className="w-3.5 h-3.5" />
                <span>{isInspecting ? 'Inspecting...' : 'Select Original Theme .ZIP'}</span>
              </button>
              <p className="text-[11px] text-slate-500 mt-2">
                <Lock className="w-3 h-3 inline mr-1 text-emerald-600" />
                100% browser-local · Preserves original <code className="font-mono">single.php</code>
              </p>
            </div>
            {errorMsg && <p className="mt-2 text-xs text-red-600 font-medium">{errorMsg}</p>}
          </div>
        </div>

        {report && (
          <div className="mt-5 pt-5 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div className="text-xs">
              <p className="font-semibold text-emerald-700 flex items-center gap-1.5">
                <CheckCircle2 className="w-4 h-4" />
                <span>
                  Detected {report.detectedThemeName} v{report.detectedVersion} · Exact Folder:{' '}
                  <code className="font-mono">{report.detectedRootFolder}</code>
                </span>
              </p>
              <p className="text-slate-500 font-mono text-[11px] mt-0.5 break-all">
                SHA-256: {report.sha256Hex}
              </p>
            </div>

            <div className="flex flex-wrap gap-2.5">
              <button
                type="button"
                onClick={handleDownloadOriginalBackup}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold whitespace-nowrap"
              >
                <Archive className="w-3.5 h-3.5" />
                <span>Original Backup</span>
              </button>
              <button
                type="button"
                disabled={isExporting}
                onClick={() => void handleDownloadVerifiedChildTheme()}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold whitespace-nowrap"
              >
                <Download className="w-3.5 h-3.5" />
                <span>Matched Child Theme</span>
              </button>
              <button
                type="button"
                disabled={isExporting}
                onClick={() => void handleDownloadMergedClone()}
                className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold whitespace-nowrap"
              >
                <ShieldCheck className="w-3.5 h-3.5" />
                <span>Merged Theme ZIP</span>
              </button>
              <button
                type="button"
                onClick={() => fileInputRef.current?.click()}
                className="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 whitespace-nowrap"
              >
                <Upload className="w-3.5 h-3.5" />
                <span>Replace</span>
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};
