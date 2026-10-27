import React, { useState } from 'react';
import { Download } from 'lucide-react';
import {
  buildStandaloneThemeZipBlob,
  InspectedThemeReport,
  triggerBlobDownload,
} from './data/wpThemeRegistry';
import { ThemeZipInspector } from './components/ThemeZipInspector';
import { EssentialPagesPreview } from './components/EssentialPagesPreview';
import { SinglePostPreview } from './components/SinglePostPreview';
import { StagingHomepagePreview } from './components/StagingHomepagePreview';
import { TestingAndDeploymentAudit } from './components/CodeAndChecklistPanel';

type ActiveTab = 'inspector' | 'pages' | 'single' | 'preview' | 'audit';

const WORKBENCH_TABS = [
  { id: 'inspector', label: 'Install' },
  { id: 'pages', label: 'Pages & Authors' },
  { id: 'single', label: 'Single Post' },
  { id: 'preview', label: 'Homepage' },
  { id: 'audit', label: 'Code & Audit' },
] as const;

export default function App() {
  const [activeTab, setActiveTab] = useState<ActiveTab>('inspector');
  const [inspectedReport, setInspectedReport] = useState<InspectedThemeReport | null>(null);
  const [isDownloading, setIsDownloading] = useState(false);

  const handleQuickDownload = async () => {
    setIsDownloading(true);
    try {
      const blob = await buildStandaloneThemeZipBlob();
      triggerBlobDownload(blob, 'promptsyra-v2.4.1.zip');
    } finally {
      setIsDownloading(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col bg-slate-50 text-slate-900">
      {/* Strict 3-Zone Top Bar Contract */}
      <header className="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div className="max-w-[1360px] mx-auto px-6 h-16 flex items-center justify-between gap-8">
          {/* Zone 1: Single-line Brand Title */}
          <button
            type="button"
            onClick={() => setActiveTab('inspector')}
            className="font-display font-bold text-base tracking-tight text-slate-900 whitespace-nowrap shrink-0"
          >
            PromptSyra WP Theme
          </button>

          {/* Zone 2: 5 Concise Single-Line Navigation Links */}
          <nav
            aria-label="Workbench Sections"
            className="hidden lg:flex items-center gap-6 text-sm font-medium text-slate-600"
          >
            {WORKBENCH_TABS.map((item) => (
              <button
                key={item.id}
                type="button"
                onClick={() => setActiveTab(item.id)}
                className={`py-1 border-b-2 transition-colors whitespace-nowrap shrink-0 ${
                  activeTab === item.id
                    ? 'border-violet-600 text-slate-900 font-semibold'
                    : 'border-transparent hover:text-slate-900'
                }`}
              >
                {item.label}
              </button>
            ))}
          </nav>

          {/* Zone 3: 1 Primary Action */}
          <div className="flex items-center shrink-0">
            <button
              type="button"
              disabled={isDownloading}
              onClick={() => void handleQuickDownload()}
              className="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors whitespace-nowrap shrink-0"
            >
              <Download className="w-3.5 h-3.5" />
              <span>Download Theme v2.4.1 ZIP</span>
            </button>
          </div>
        </div>

        {/* Mobile / Tablet Tab Switcher */}
        <div className="lg:hidden border-t border-slate-100 px-4 py-2 flex items-center gap-2 overflow-x-auto">
          {WORKBENCH_TABS.map((item) => (
            <button
              key={item.id}
              type="button"
              onClick={() => setActiveTab(item.id)}
              className={`px-3 py-1.5 rounded-md text-xs font-semibold whitespace-nowrap shrink-0 ${
                activeTab === item.id
                  ? 'bg-violet-600 text-white'
                  : 'bg-slate-100 text-slate-700'
              }`}
            >
              {item.label}
            </button>
          ))}
        </div>
      </header>

      {/* Main Content Viewport */}
      <main className="flex-1 max-w-[1360px] w-full mx-auto px-6 py-7">
        {activeTab === 'inspector' && (
          <ThemeZipInspector
            report={inspectedReport}
            onReportGenerated={(rep) => setInspectedReport(rep)}
          />
        )}
        {activeTab === 'pages' && <EssentialPagesPreview />}
        {activeTab === 'single' && <SinglePostPreview />}
        {activeTab === 'preview' && <StagingHomepagePreview />}
        {activeTab === 'audit' && <TestingAndDeploymentAudit report={inspectedReport} />}
      </main>

      <footer className="border-t border-slate-200 bg-white py-4 px-6">
        <div className="max-w-[1360px] mx-auto flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
          <span>
            PromptSyra.com WordPress Theme Package · Version 2.4.1 (Header Navigation Upgrade + Important Pages Dropdown)
          </span>
          <span>Official Contact: contact@promptsyra.com · Zero Credentials Stored</span>
        </div>
      </footer>
    </div>
  );
}
