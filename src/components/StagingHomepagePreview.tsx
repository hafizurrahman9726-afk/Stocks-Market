import React, { useMemo, useState } from 'react';
import {
  ArrowRight,
  Camera,
  Check,
  ChevronDown,
  Clipboard,
  Info,
  Layers,
  Menu,
  Monitor,
  Plus,
  Minus,
  Search,
  SlidersHorizontal,
  Smartphone,
  Sparkles,
  Sun,
  Tablet,
  TrendingUp,
  User,
} from 'lucide-react';

interface SimulatedWpPost {
  id: number;
  title: string;
  categoryName: string;
  categorySlug: 'ai-photo-prompts' | 'trending-prompts' | 'social-media-prompts';
  date: string;
  excerpt: string;
  palette: string;
  framingNote: string;
}

const SIMULATED_WP_POSTS: SimulatedWpPost[] = [
  {
    id: 101,
    title: 'Natural Window Portrait Prompts With 85mm Shallow Depth of Field',
    categoryName: 'AI Photo Prompts',
    categorySlug: 'ai-photo-prompts',
    date: 'October 2026',
    excerpt:
      'Structured portrait phrasing specifying soft directional window light, neutral studio backdrop textures, and natural skin tone balance.',
    palette: 'from-indigo-100 via-violet-50 to-slate-100',
    framingNote: '85mm · Soft Key Light',
  },
  {
    id: 102,
    title: 'Golden Hour Street & Editorial Couple Photography Prompts',
    categoryName: 'Trending Prompts',
    categorySlug: 'trending-prompts',
    date: 'October 2026',
    excerpt:
      'Warm evening backlight compositions with customizable wardrobe palettes, candid walking angles, and subtle urban depth.',
    palette: 'from-amber-50 via-violet-50 to-indigo-100',
    framingNote: '50mm · Warm Backlight',
  },
  {
    id: 103,
    title: 'Vertical 9:16 Creator Thumbnail & Story Backdrop Prompts',
    categoryName: 'Social Media Prompts',
    categorySlug: 'social-media-prompts',
    date: 'October 2026',
    excerpt:
      'Clean negative-space compositions designed for vertical feeds, reels covers, and profile banners with clear focal contrast.',
    palette: 'from-sky-50 via-indigo-50 to-violet-100',
    framingNote: '9:16 · Clean Negative Space',
  },
  {
    id: 104,
    title: 'Minimalist Monochrome Studio Headshot Lighting Setups',
    categoryName: 'AI Photo Prompts',
    categorySlug: 'ai-photo-prompts',
    date: 'September 2026',
    excerpt:
      'Classic key-and-rim monochrome portrait formulas with crisp catchlights and tonal separation for editorial profiles.',
    palette: 'from-slate-200 via-slate-100 to-zinc-100',
    framingNote: 'Monochrome · Rim Light',
  },
  {
    id: 105,
    title: 'Festive Cultural Attire & Courtyard Lantern Portrait Ideas',
    categoryName: 'Trending Prompts',
    categorySlug: 'trending-prompts',
    date: 'September 2026',
    excerpt:
      'Balanced ambient lantern glow paired with detailed fabric texture notes and customizable color combinations.',
    palette: 'from-purple-100 via-fuchsia-50 to-amber-50',
    framingNote: '35mm · Ambient Glow',
  },
  {
    id: 106,
    title: 'Product Flat-Lay & Desk Aesthetic Prompts for Carousel Posts',
    categoryName: 'Social Media Prompts',
    categorySlug: 'social-media-prompts',
    date: 'September 2026',
    excerpt:
      'Overhead daylight tabletop arrangements for lifestyle carousels, creator announcements, and clean brand visuals.',
    palette: 'from-violet-100 via-slate-50 to-indigo-50',
    framingNote: 'Overhead · Diffused Daylight',
  },
];

const DEFAULT_FAQS = [
  {
    q: 'What is PromptSyra.com?',
    a: 'PromptSyra.com is an editorial library of structured AI photo prompts covering portraits, creative studio photography, and social media visuals, paired with practical customization notes.',
  },
  {
    q: 'How do I copy and use prompts from PromptSyra?',
    a: 'Open any prompt article, review the framing and lighting notes, use the copy button (or select the prompt text directly), and paste it into your preferred image-generation tool.',
  },
  {
    q: 'Which image-generation tools work with these photo prompts?',
    a: 'Our prompts are written in clear natural language suitable for modern text-to-image tools that accept descriptive photography prompts. Because each platform interprets lighting, aspect ratios, and styles differently, results will vary by tool and model version.',
  },
  {
    q: 'How can I customize a photo prompt for my own concept?',
    a: 'Start with the base prompt structure and replace specific details such as outfit colors, camera focal length (for example 35mm vs. 85mm), background setting, or time-of-day lighting while keeping the composition clear.',
  },
  {
    q: 'Are the prompts on PromptSyra free to browse and copy?',
    a: 'Yes. Visitors can browse our published categories, read the customization guides, and copy prompt text directly from articles without registering an account.',
  },
  {
    q: 'Tips for getting clearer, more natural-looking AI portrait results?',
    a: 'Keep subject descriptions concise, specify a realistic light source (such as soft window light or golden-hour rim light), avoid conflicting camera angles in the same prompt, and adjust aspect ratios inside your generator settings.',
  },
];

export const StagingHomepagePreview: React.FC = () => {
  const [viewport, setViewport] = useState<'desktop' | 'tablet' | 'mobile'>('desktop');
  const [openDropdown, setOpenDropdown] = useState<'important-pages' | null>(null);
  const [headerSearchOpen, setHeaderSearchOpen] = useState(false);
  const [mobileDrawerOpen, setMobileDrawerOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [activeCategory, setActiveCategory] = useState<
    'all' | 'ai-photo-prompts' | 'trending-prompts' | 'social-media-prompts'
  >('all');
  const [openFaqIdx, setOpenFaqIdx] = useState<number | null>(0);
  const [previewNotice, setPreviewNotice] = useState<string | null>(null);
  const [sampleCopied, setSampleCopied] = useState(false);

  const samplePromptText =
    'Editorial three-quarter portrait of a young woman wearing a warm oatmeal knit sweater, captured from eye level with an 85mm prime lens and shallow f/1.8 depth of field, illuminated by soft directional north-window daylight with gentle shadow roll-off, set against a muted warmstone studio backdrop with natural skin texture and balanced filmic color tones.';

  const handleCopySamplePrompt = async () => {
    try {
      await navigator.clipboard.writeText(samplePromptText);
    } catch {
      const area = document.createElement('textarea');
      area.value = samplePromptText;
      document.body.appendChild(area);
      area.select();
      document.execCommand('copy');
      document.body.removeChild(area);
    }
    setSampleCopied(true);
    setTimeout(() => setSampleCopied(false), 2000);
  };

  // WordPress Customizer state simulators to verify conditional theme logic
  const [simulateToolsMenuAssigned, setSimulateToolsMenuAssigned] = useState(false);
  const [simulateOfficialSocialsConfigured, setSimulateOfficialSocialsConfigured] = useState(false);
  const [simulateAdminLoggedIn, setSimulateAdminLoggedIn] = useState(true);

  const filteredPosts = useMemo(() => {
    return SIMULATED_WP_POSTS.filter((post) => {
      const matchesCat = activeCategory === 'all' || post.categorySlug === activeCategory;
      const q = searchQuery.trim().toLowerCase();
      const matchesSearch =
        q.length === 0 ||
        post.title.toLowerCase().includes(q) ||
        post.excerpt.toLowerCase().includes(q) ||
        post.categoryName.toLowerCase().includes(q);
      return matchesCat && matchesSearch;
    });
  }, [activeCategory, searchQuery]);

  const handleSimulatedNav = (destinationLabel: string, wpRoute: string) => {
    setOpenDropdown(null);
    setMobileDrawerOpen(false);
    setPreviewNotice(
      `WordPress route resolved: ${destinationLabel} → ${wpRoute} (Existing single.php & permalinks remain 100% preserved).`
    );
  };

  const containerWidthClass =
    viewport === 'mobile'
      ? 'max-w-[390px]'
      : viewport === 'tablet'
        ? 'max-w-[820px]'
        : 'max-w-full';

  return (
    <div className="space-y-4">
      {/* Customizer & Viewport Control Toolbar */}
      <div className="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div className="flex flex-wrap items-center gap-3">
          <span className="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700">
            <SlidersHorizontal className="w-3.5 h-3.5 text-violet-600" />
            <span>WP Customizer Conditional Preview:</span>
          </span>

          <label className="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer select-none">
            <input
              type="checkbox"
              checked={simulateOfficialSocialsConfigured}
              onChange={(e) => setSimulateOfficialSocialsConfigured(e.target.checked)}
              className="rounded border-slate-300 text-violet-600 focus:ring-violet-500"
            />
            <span>Official Social URLs Configured</span>
          </label>

          <label className="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer select-none">
            <input
              type="checkbox"
              checked={simulateToolsMenuAssigned}
              onChange={(e) => setSimulateToolsMenuAssigned(e.target.checked)}
              className="rounded border-slate-300 text-violet-600 focus:ring-violet-500"
            />
            <span>Real WP Tools Menu Assigned</span>
          </label>

          <label className="inline-flex items-center gap-2 text-xs text-slate-600 cursor-pointer select-none">
            <input
              type="checkbox"
              checked={simulateAdminLoggedIn}
              onChange={(e) => setSimulateAdminLoggedIn(e.target.checked)}
              className="rounded border-slate-300 text-violet-600 focus:ring-violet-500"
            />
            <span>Show Admin Missing-Bio Hints</span>
          </label>
        </div>

        <div className="flex items-center gap-1 p-1 bg-slate-100 rounded-lg">
          <button
            type="button"
            onClick={() => setViewport('desktop')}
            className={`inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors whitespace-nowrap shrink-0 ${
              viewport === 'desktop' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <Monitor className="w-3.5 h-3.5" />
            <span>Desktop</span>
          </button>
          <button
            type="button"
            onClick={() => setViewport('tablet')}
            className={`inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors whitespace-nowrap shrink-0 ${
              viewport === 'tablet' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <Tablet className="w-3.5 h-3.5" />
            <span>Tablet</span>
          </button>
          <button
            type="button"
            onClick={() => setViewport('mobile')}
            className={`inline-flex items-center gap-1.5 px-2.5 py-1.5 text-xs font-medium rounded-md transition-colors whitespace-nowrap shrink-0 ${
              viewport === 'mobile' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900'
            }`}
          >
            <Smartphone className="w-3.5 h-3.5" />
            <span>Mobile</span>
          </button>
        </div>
      </div>

      {previewNotice && (
        <div className="bg-violet-50 border border-violet-200 rounded-lg px-4 py-2.5 flex items-center justify-between gap-4 text-xs text-slate-800">
          <span>{previewNotice}</span>
          <button
            type="button"
            onClick={() => setPreviewNotice(null)}
            className="font-semibold text-violet-700 hover:text-violet-900 whitespace-nowrap shrink-0"
          >
            Dismiss
          </button>
        </div>
      )}

      {/* Rendered WordPress front-page.php Staging Canvas */}
      <div
        className={`mx-auto ${containerWidthClass} border border-slate-200 rounded-2xl overflow-hidden bg-white shadow-xs transition-all`}
      >
        {/* A. Header & Navigation */}
        <header className="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-[#E5E7F3]">
          <div className="max-w-[1160px] mx-auto px-5 h-[70px] flex items-center justify-between gap-6">
            {/* Zone 1: Brand Wordmark */}
            <button
              type="button"
              onClick={() => handleSimulatedNav('Home', '/')}
              className="inline-flex items-center gap-2.5 text-left whitespace-nowrap shrink-0 focus-visible:outline-2 focus-visible:outline-violet-600 rounded-md"
            >
              <span className="w-8 h-8 rounded-lg bg-gradient-to-br from-[#5B3DF5] to-blue-500 text-white font-display font-extrabold text-base inline-flex items-center justify-center">
                P
              </span>
              <span className="font-display font-extrabold text-xl tracking-tight text-[#0F172A]">
                Prompt<span className="text-[#5B3DF5]">Syra</span>
                <span className="text-sm font-semibold text-slate-500">.com</span>
              </span>
            </button>

            {/* Zone 2: Primary Navigation (Hidden on narrow viewports) */}
            {viewport === 'desktop' && (
              <nav aria-label="Primary Navigation" className="flex items-center gap-5 text-sm font-medium text-slate-600">
                <button
                  type="button"
                  onClick={() => handleSimulatedNav('Home', '/')}
                  className="text-[#5B3DF5] font-semibold whitespace-nowrap shrink-0"
                >
                  Home
                </button>

                <button
                  type="button"
                  onClick={() => {
                    setActiveCategory('ai-photo-prompts');
                    handleSimulatedNav('AI Photo Prompts', '/category/ai-photo-prompts/');
                  }}
                  className={`hover:text-[#5B3DF5] transition-colors whitespace-nowrap shrink-0 ${
                    activeCategory === 'ai-photo-prompts' ? 'text-[#5B3DF5] font-semibold' : ''
                  }`}
                >
                  AI Photo Prompts
                </button>

                <button
                  type="button"
                  onClick={() => {
                    setActiveCategory('trending-prompts');
                    handleSimulatedNav('Trending Prompts', '/category/trending-prompts/');
                  }}
                  className={`hover:text-[#5B3DF5] transition-colors whitespace-nowrap shrink-0 ${
                    activeCategory === 'trending-prompts' ? 'text-[#5B3DF5] font-semibold' : ''
                  }`}
                >
                  Trending Prompts
                </button>

                <button
                  type="button"
                  onClick={() => {
                    setActiveCategory('social-media-prompts');
                    handleSimulatedNav('Social Media Prompts', '/category/social-media-prompts/');
                  }}
                  className={`hover:text-[#5B3DF5] transition-colors whitespace-nowrap shrink-0 ${
                    activeCategory === 'social-media-prompts' ? 'text-[#5B3DF5] font-semibold' : ''
                  }`}
                >
                  Social Media Prompts
                </button>

                {/* Important Pages Dropdown */}
                <div className="relative">
                  <button
                    type="button"
                    aria-expanded={openDropdown === 'important-pages'}
                    onClick={() =>
                      setOpenDropdown(openDropdown === 'important-pages' ? null : 'important-pages')
                    }
                    className="inline-flex items-center gap-1 hover:text-[#5B3DF5] transition-colors whitespace-nowrap shrink-0"
                  >
                    <span>Important Pages</span>
                    <ChevronDown
                      className={`w-3.5 h-3.5 transition-transform ${
                        openDropdown === 'important-pages' ? 'rotate-180' : ''
                      }`}
                    />
                  </button>
                  {openDropdown === 'important-pages' && (
                    <div className="absolute left-0 top-full mt-2 w-56 bg-white border border-[#E5E7F3] rounded-xl shadow-lg p-1.5 z-40">
                      <button
                        type="button"
                        onClick={() => {
                          setOpenDropdown(null);
                          handleSimulatedNav('About Us', '/about-us/');
                        }}
                        className="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-[#F6F5FF] hover:text-[#5B3DF5] rounded-lg transition-colors"
                      >
                        About Us
                      </button>
                      <button
                        type="button"
                        onClick={() => {
                          setOpenDropdown(null);
                          handleSimulatedNav('Contact Us', '/contact-us/');
                        }}
                        className="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-[#F6F5FF] hover:text-[#5B3DF5] rounded-lg transition-colors"
                      >
                        Contact Us
                      </button>
                      <button
                        type="button"
                        onClick={() => {
                          setOpenDropdown(null);
                          handleSimulatedNav('Privacy Policy', '/privacy-policy/');
                        }}
                        className="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-[#F6F5FF] hover:text-[#5B3DF5] rounded-lg transition-colors"
                      >
                        Privacy Policy
                      </button>
                      <button
                        type="button"
                        onClick={() => {
                          setOpenDropdown(null);
                          handleSimulatedNav('Disclaimer', '/disclaimer/');
                        }}
                        className="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-[#F6F5FF] hover:text-[#5B3DF5] rounded-lg transition-colors"
                      >
                        Disclaimer
                      </button>
                      <button
                        type="button"
                        onClick={() => {
                          setOpenDropdown(null);
                          handleSimulatedNav('Terms & Conditions', '/terms-and-conditions/');
                        }}
                        className="w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-[#F6F5FF] hover:text-[#5B3DF5] rounded-lg transition-colors"
                      >
                        Terms &amp; Conditions
                      </button>
                    </div>
                  )}
                </div>
              </nav>
            )}

            {/* Zone 3: Search & Explore Prompts CTA */}
            <div className="flex items-center gap-2.5 shrink-0">
              <button
                type="button"
                aria-label="Toggle Prompt Search"
                aria-expanded={headerSearchOpen}
                onClick={() => setHeaderSearchOpen(!headerSearchOpen)}
                className="w-9 h-9 rounded-lg border border-[#E5E7F3] inline-flex items-center justify-center text-slate-700 hover:border-[#5B3DF5] hover:text-[#5B3DF5] transition-colors"
              >
                <Search className="w-4 h-4" />
              </button>

              {viewport === 'desktop' ? (
                <a
                  href="#staging-featured-prompts"
                  className="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#5B3DF5] hover:bg-[#492CD9] text-white font-display text-sm font-semibold transition-colors whitespace-nowrap shrink-0"
                >
                  <span>Explore Prompts</span>
                  <ArrowRight className="w-4 h-4" />
                </a>
              ) : (
                <button
                  type="button"
                  aria-label="Toggle Mobile Navigation"
                  aria-expanded={mobileDrawerOpen}
                  onClick={() => setMobileDrawerOpen(!mobileDrawerOpen)}
                  className="w-9 h-9 rounded-lg border border-[#E5E7F3] inline-flex items-center justify-center text-slate-700"
                >
                  <Menu className="w-4 h-4" />
                </button>
              )}
            </div>
          </div>

          {headerSearchOpen && (
            <div className="bg-[#F6F5FF] border-t border-[#E5E7F3] px-5 py-3">
              <div className="max-w-[640px] mx-auto flex gap-2">
                <input
                  type="search"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search prompts by style, lighting, or subject..."
                  className="flex-1 bg-white border border-[#E5E7F3] rounded-lg px-3.5 py-2 text-sm text-slate-900"
                />
                <button
                  type="button"
                  onClick={() => setHeaderSearchOpen(false)}
                  className="px-4 py-2 rounded-lg bg-[#5B3DF5] text-white text-sm font-semibold whitespace-nowrap shrink-0"
                >
                  Search
                </button>
              </div>
            </div>
          )}

          {viewport !== 'desktop' && mobileDrawerOpen && (
            <div className="bg-white border-t border-[#E5E7F3] px-5 py-4 space-y-2 text-sm">
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('Home', '/');
                }}
                className="block w-full text-left py-1.5 font-semibold text-[#5B3DF5]"
              >
                Home
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  setActiveCategory('ai-photo-prompts');
                  handleSimulatedNav('AI Photo Prompts', '/category/ai-photo-prompts/');
                }}
                className="block w-full text-left py-1.5 text-slate-700 hover:text-[#5B3DF5]"
              >
                AI Photo Prompts
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  setActiveCategory('trending-prompts');
                  handleSimulatedNav('Trending Prompts', '/category/trending-prompts/');
                }}
                className="block w-full text-left py-1.5 text-slate-700 hover:text-[#5B3DF5]"
              >
                Trending Prompts
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  setActiveCategory('social-media-prompts');
                  handleSimulatedNav('Social Media Prompts', '/category/social-media-prompts/');
                }}
                className="block w-full text-left py-1.5 text-slate-700 hover:text-[#5B3DF5]"
              >
                Social Media Prompts
              </button>
              <p className="pt-2 text-xs font-semibold uppercase tracking-wider text-slate-400">Important Pages</p>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('About Us', '/about-us/');
                }}
                className="block w-full text-left py-1 text-slate-600 hover:text-[#5B3DF5]"
              >
                About Us
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('Contact Us', '/contact-us/');
                }}
                className="block w-full text-left py-1 text-slate-600 hover:text-[#5B3DF5]"
              >
                Contact Us
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('Privacy Policy', '/privacy-policy/');
                }}
                className="block w-full text-left py-1 text-slate-600 hover:text-[#5B3DF5]"
              >
                Privacy Policy
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('Disclaimer', '/disclaimer/');
                }}
                className="block w-full text-left py-1 text-slate-600 hover:text-[#5B3DF5]"
              >
                Disclaimer
              </button>
              <button
                type="button"
                onClick={() => {
                  setMobileDrawerOpen(false);
                  handleSimulatedNav('Terms & Conditions', '/terms-and-conditions/');
                }}
                className="block w-full text-left py-1 text-slate-600 hover:text-[#5B3DF5]"
              >
                Terms &amp; Conditions
              </button>
            </div>
          )}
        </header>

        {/* B. Hero Section */}
        <section className="bg-gradient-to-br from-[#F6F5FF] via-white to-[#ECE9FF] border-b border-[#E5E7F3] py-12 px-5">
          <div
            className={`max-w-[1160px] mx-auto grid gap-10 items-center ${
              viewport === 'desktop' ? 'grid-cols-12' : 'grid-cols-1'
            }`}
          >
            <div className={viewport === 'desktop' ? 'col-span-7' : ''}>
              <p className="font-display text-xs font-bold text-[#5B3DF5] mb-2.5">
                AI Photo Prompts &amp; Creative Ideas
              </p>
              <h1 className="font-display font-extrabold text-3xl md:text-[44px] leading-[1.12] tracking-tight text-[#0F172A] mb-4 [text-wrap:balance]">
                Turn Your Ideas Into <span className="text-[#5B3DF5]">Stunning</span> AI Creations
              </h1>
              <p className="text-base text-slate-600 max-w-xl mb-6 leading-relaxed">
                Discover useful AI photo prompts for portraits, creative photography, social media visuals, and more.
              </p>

              {/* Working Prompt Search Form */}
              <form
                onSubmit={(e) => {
                  e.preventDefault();
                  const el = document.getElementById('staging-featured-prompts');
                  el?.scrollIntoView({ behavior: 'smooth' });
                }}
                className="flex items-center gap-2 bg-white border border-[#E5E7F3] rounded-xl p-1.5 pl-3.5 max-w-xl shadow-xs"
              >
                <Search className="w-4 h-4 text-slate-400 shrink-0" />
                <input
                  type="search"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search photo prompts (portraits, studio lighting, social media...)"
                  className="flex-1 min-w-0 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none py-1.5"
                />
                <button
                  type="submit"
                  className="px-4 py-2 rounded-lg bg-[#5B3DF5] hover:bg-[#492CD9] text-white font-display text-sm font-semibold transition-colors whitespace-nowrap shrink-0"
                >
                  Search
                </button>
              </form>

              {/* Section 1 (v2.4.0): Integrated Hero Supporting CTA & Feature Benefits Bar */}
              <div className="mt-5 max-w-xl bg-white/90 backdrop-blur border border-[#DDD9F5] rounded-2xl overflow-hidden shadow-xs">
                <div className="px-4 py-3 bg-white border-b border-[#EAE8F8] flex flex-wrap items-center justify-between gap-3">
                  <a
                    href="#staging-featured-prompts"
                    className="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0F172A] hover:bg-[#5B3DF5] text-white font-display text-xs font-bold transition-colors whitespace-nowrap group"
                  >
                    <span>Explore Prompts</span>
                    <ArrowRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />
                  </a>
                  <span className="text-xs font-medium text-slate-500">
                    Browse published collections &amp; adaptation notes
                  </span>
                </div>

                <ul
                  aria-label="PromptSyra Feature Benefits"
                  className={`bg-[#FAF9FF] grid ${
                    viewport === 'mobile'
                      ? 'grid-cols-1 divide-y divide-[#EAE8F8]'
                      : 'grid-cols-3 divide-x divide-[#EAE8F8]'
                  }`}
                >
                  {[
                    'Structured, Copy-Ready Text',
                    'Portrait, Creative & Social Styles',
                    'Practical Customization Notes',
                  ].map((benefit) => (
                    <li
                      key={benefit}
                      className="px-3.5 py-3 flex items-center gap-2 text-xs font-semibold text-slate-800 leading-snug"
                    >
                      <span className="w-5 h-5 rounded-md bg-[#ECE9FF] text-[#5B3DF5] inline-flex items-center justify-center shrink-0">
                        <Check className="w-3 h-3 stroke-[2.6]" />
                      </span>
                      <span>{benefit}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>

            {/* Photography Visual Composition with 3 Real Images */}
            <div className={viewport === 'desktop' ? 'col-span-5' : ''}>
              <div className={`grid gap-3 ${viewport === 'mobile' ? 'grid-cols-1' : 'grid-cols-12'}`}>
                {/* Image 1: Main Portrait Card */}
                <div className={`${viewport === 'mobile' ? 'col-span-1' : 'col-span-7'} bg-white border border-[#E5E7F3] rounded-2xl overflow-hidden flex flex-col shadow-xs group`}>
                  <div className="relative overflow-hidden bg-violet-50">
                    <img
                      src="https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-hero-portrait.webp"
                      alt="Portrait Study · 85mm Soft Rim Light AI Photography Prompt Example"
                      className="w-full h-52 md:h-60 object-cover object-center transition-transform duration-300 group-hover:scale-102"
                      loading="eager"
                    />
                  </div>
                  <div className="p-3.5 flex-1 flex flex-col justify-center">
                    <p className="font-display text-xs font-bold text-[#0F172A]">
                      Portrait Study · 85mm Soft Rim Light
                    </p>
                    <p className="text-[11px] text-slate-500 mt-1 leading-relaxed">
                      &ldquo;Natural window key light, shallow depth of field, warm editorial skin tones, 85mm f/1.8...&rdquo;
                    </p>
                  </div>
                </div>

                {/* Right Stack: Creative Studio & 9:16 Social Visuals */}
                <div className={`${viewport === 'mobile' ? 'col-span-1 grid grid-cols-2' : 'col-span-5 flex flex-col'} gap-3`}>
                  {/* Image 2: Creative Studio Card */}
                  <div className="flex-1 bg-white border border-[#E5E7F3] rounded-2xl overflow-hidden flex flex-col shadow-xs group">
                    <div className="relative overflow-hidden bg-violet-50">
                      <img
                        src="https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-creative-studio.webp"
                        alt="Creative Studio Setup AI Photography Prompt Example"
                        className="w-full h-24 md:h-28 object-cover object-center transition-transform duration-300 group-hover:scale-102"
                        loading="eager"
                      />
                    </div>
                    <div className="p-2.5 flex-1 flex items-center">
                      <p className="font-display text-[11px] font-bold text-[#0F172A] leading-tight">Creative Studio Setup</p>
                    </div>
                  </div>

                  {/* Image 3: Social Visuals Card */}
                  <div className="flex-1 bg-white border border-[#E5E7F3] rounded-2xl overflow-hidden flex flex-col shadow-xs group">
                    <div className="relative overflow-hidden bg-violet-50">
                      <img
                        src="https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-social-visuals.webp"
                        alt="9:16 Social Visuals AI Photography Prompt Example"
                        className="w-full h-24 md:h-28 object-cover object-center transition-transform duration-300 group-hover:scale-102"
                        loading="lazy"
                      />
                    </div>
                    <div className="p-2.5 flex-1 flex items-center">
                      <p className="font-display text-[11px] font-bold text-[#0F172A] leading-tight">9:16 Social Visuals</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        {/* Section 2 (v2.4.0): Three Primary Editorial Category Discovery Cards */}
        <section className="pt-11 pb-6 px-5">
          <div className="max-w-[1160px] mx-auto">
            <div className="flex flex-wrap items-end justify-between gap-4 mb-6">
              <div>
                <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">Browse by Visual Focus</p>
                <h2 className="font-display font-extrabold text-2xl text-[#0F172A]">Primary Prompt Collections</h2>
              </div>
              <p className="text-xs text-slate-600 max-w-md">
                Three focused collections organized by photography style, creative direction, and display format.
              </p>
            </div>

            <div
              className={`grid gap-5 ${
                viewport === 'mobile' ? 'grid-cols-1' : viewport === 'tablet' ? 'grid-cols-2' : 'grid-cols-3'
              }`}
            >
              {[
                {
                  name: 'AI Photo Prompts',
                  slug: 'ai-photo-prompts' as const,
                  liveRoute: '/category/ai-photo-prompts/',
                  focusLine: 'Portraits · Studio Lighting · Realism',
                  desc: 'Portrait framing, studio and natural lighting setups, and realistic creative photography prompts.',
                  metaLabel: '1 Published Guide',
                  ctaLabel: 'Explore Photo Prompts',
                  bandClass: 'from-[#F3F1FF] to-[#EEF2FF]',
                  icon: Camera,
                },
                {
                  name: 'Trending Prompts',
                  slug: 'trending-prompts' as const,
                  liveRoute: '/category/trending-prompts/',
                  focusLine: 'Seasonal Concepts · Emerging Styles',
                  desc: 'Seasonal photography concepts, emerging visual styles, and timely creative scene ideas.',
                  metaLabel: '/trending-prompts/',
                  ctaLabel: 'Explore Seasonal Ideas',
                  bandClass: 'from-[#FAF5FF] to-[#FFF7ED]',
                  icon: Sparkles,
                },
                {
                  name: 'Social Media Prompts',
                  slug: 'social-media-prompts' as const,
                  liveRoute: '/category/ai-social-media-prompts/',
                  focusLine: '9:16 Stories · Reels · Thumbnails',
                  desc: 'Vertical 9:16 stories, reel covers, creator thumbnails, and feed-ready visual compositions.',
                  metaLabel: '/ai-social-media-prompts/',
                  ctaLabel: 'Explore Social Visuals',
                  bandClass: 'from-[#F0F9FF] to-[#F3F1FF]',
                  icon: Smartphone,
                },
              ].map((cat) => {
                const Icon = cat.icon;
                return (
                  <button
                    key={cat.slug}
                    type="button"
                    onClick={() => {
                      setActiveCategory(cat.slug);
                      handleSimulatedNav(cat.name, cat.liveRoute);
                    }}
                    className="group text-left rounded-2xl border border-[#E5E7F3] hover:border-[#BCB3FA] hover:-translate-y-0.5 hover:shadow-md bg-white transition-all overflow-hidden flex flex-col focus-visible:outline-2 focus-visible:outline-[#5B3DF5]"
                  >
                    <div
                      className={`w-full px-5 py-4 bg-gradient-to-br ${cat.bandClass} border-b border-[#EEF0F8] flex items-center justify-between gap-3`}
                    >
                      <span className="w-11 h-11 rounded-xl bg-white border border-violet-200/60 text-[#5B3DF5] inline-flex items-center justify-center shrink-0 shadow-2xs">
                        <Icon className="w-5 h-5" />
                      </span>
                      <span className="font-display text-[11px] font-bold text-slate-600 text-right">
                        {cat.focusLine}
                      </span>
                    </div>

                    <div className="p-5 flex-1">
                      <h3 className="font-display font-extrabold text-lg text-[#0F172A] group-hover:text-[#5B3DF5] transition-colors">
                        {cat.name}
                      </h3>
                      <p className="text-xs text-slate-600 mt-1.5 leading-relaxed">{cat.desc}</p>
                    </div>

                    <div className="w-full px-5 py-3 bg-[#FCFCFF] border-t border-[#F1F3F9] flex items-center justify-between gap-3">
                      <span className="text-[11px] font-medium text-slate-500 font-mono">{cat.metaLabel}</span>
                      <span className="inline-flex items-center gap-1 font-display text-xs font-bold text-[#5B3DF5]">
                        <span>{cat.ctaLabel}</span>
                        <ArrowRight className="w-3.5 h-3.5 transition-transform group-hover:translate-x-0.5" />
                      </span>
                    </div>
                  </button>
                );
              })}
            </div>
          </div>
        </section>

        {/* D. Featured AI Prompts Section */}
        <section id="staging-featured-prompts" className="py-8 px-5">
          <div className="max-w-[1160px] mx-auto">
            <div className="flex flex-wrap items-end justify-between gap-4 mb-6">
              <div>
                <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">Published WordPress Posts</p>
                <h2 className="font-display font-extrabold text-2xl text-[#0F172A]">Featured AI Prompts</h2>
              </div>

              {/* First category choices: AI Photo Prompts, Trending Prompts, Social Media Prompts */}
              <div className="flex flex-wrap items-center gap-1 p-1 bg-[#F6F5FF] border border-[#E5E7F3] rounded-xl">
                {(
                  [
                    { label: 'AI Photo Prompts', value: 'ai-photo-prompts' },
                    { label: 'Trending Prompts', value: 'trending-prompts' },
                    { label: 'Social Media Prompts', value: 'social-media-prompts' },
                    { label: 'All Latest', value: 'all' },
                  ] as const
                ).map((tab) => (
                  <button
                    key={tab.value}
                    type="button"
                    onClick={() => setActiveCategory(tab.value)}
                    className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors whitespace-nowrap shrink-0 ${
                      activeCategory === tab.value
                        ? 'bg-white text-[#0F172A] shadow-2xs'
                        : 'text-slate-600 hover:text-[#0F172A]'
                    }`}
                  >
                    {tab.label}
                  </button>
                ))}
              </div>
            </div>

            {filteredPosts.length > 0 ? (
              <div
                className={`grid gap-5 ${
                  viewport === 'mobile'
                    ? 'grid-cols-1'
                    : viewport === 'tablet'
                      ? 'grid-cols-2'
                      : 'grid-cols-3'
                }`}
              >
                {filteredPosts.map((post) => (
                  <article
                    key={post.id}
                    className="border border-[#E5E7F3] hover:border-[#5B3DF5] rounded-2xl overflow-hidden bg-white flex flex-col transition-colors"
                  >
                    <div
                      className={`aspect-[16/10] bg-gradient-to-br ${post.palette} flex flex-col items-center justify-center p-4 text-center`}
                    >
                      <span className="font-display text-xs font-bold text-[#0F172A]">
                        the_post_thumbnail(&apos;medium_large&apos;)
                      </span>
                      <span className="text-[11px] text-slate-600 mt-1">{post.framingNote}</span>
                    </div>

                    <div className="p-5 flex-1 flex flex-col">
                      {/* Unboxed clean metadata with typographic separator */}
                      <div className="flex items-center gap-2 text-xs text-slate-500 mb-2">
                        <span>{post.categoryName}</span>
                        <span aria-hidden="true">·</span>
                        <span>{post.date}</span>
                      </div>

                      <h3 className="font-display font-bold text-base text-[#0F172A] leading-snug mb-2">
                        <button
                          type="button"
                          onClick={() =>
                            handleSimulatedNav(
                              post.title,
                              `/${post.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}/ (Rendered by untouched single.php)`
                            )
                          }
                          className="text-left hover:text-[#5B3DF5] transition-colors"
                        >
                          {post.title}
                        </button>
                      </h3>

                      <p className="text-xs text-slate-600 leading-relaxed flex-1 mb-4">{post.excerpt}</p>

                      <button
                        type="button"
                        onClick={() =>
                          handleSimulatedNav(
                            post.title,
                            `/${post.title.toLowerCase().replace(/[^a-z0-9]+/g, '-')}/ (Rendered by untouched single.php)`
                          )
                        }
                        className="inline-flex items-center gap-1.5 text-xs font-display font-semibold text-[#5B3DF5] hover:text-[#492CD9]"
                      >
                        <span>View Prompt &amp; Notes</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </button>
                    </div>
                  </article>
                ))}
              </div>
            ) : (
              <div className="bg-[#F6F5FF] border border-[#E5E7F3] rounded-2xl p-8 text-center text-sm text-slate-600">
                No published articles match your current filter.{' '}
                <button
                  type="button"
                  onClick={() => {
                    setActiveCategory('all');
                    setSearchQuery('');
                  }}
                  className="font-semibold text-[#5B3DF5] underline"
                >
                  Reset filters
                </button>
              </div>
            )}
          </div>
        </section>

        {/* Section 3 (v2.4.0): Connected 3-Step AI Prompt Workflow + Cross-Tool Note */}
        <section className="py-8 px-5">
          <div className="max-w-[1160px] mx-auto bg-gradient-to-b from-[#F6F4FF] to-[#F9F8FF] border border-[#DEDBF5] rounded-2xl p-6 md:p-9">
            <div className="max-w-2xl mb-7">
              <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">Practical 3-Step Workflow</p>
              <h2 className="font-display font-extrabold text-2xl text-[#0F172A]">How to Use AI Prompts</h2>
              <p className="text-sm text-slate-600 mt-2 leading-relaxed">
                Every prompt on PromptSyra is structured in plain photographic language so you can select a visual concept, copy the text cleanly, and adapt it inside your preferred generator.
              </p>
            </div>

            <div className="relative">
              {viewport === 'desktop' && (
                <div
                  aria-hidden="true"
                  className="absolute top-6 left-12 right-12 h-px bg-gradient-to-r from-violet-200 via-[#5B3DF5]/60 to-violet-200 pointer-events-none"
                />
              )}

              <ol
                className={`relative grid gap-5 ${
                  viewport === 'mobile' ? 'grid-cols-1' : viewport === 'tablet' ? 'grid-cols-2' : 'grid-cols-3'
                }`}
              >
                {[
                  {
                    num: '01',
                    title: 'Select a Matching Style',
                    desc: 'Choose a prompt that matches the intended subject and visual style.',
                    cue: 'Portraits · Creative Scenes · Social Formats',
                    icon: Camera,
                  },
                  {
                    num: '02',
                    title: 'Copy & Review Notes',
                    desc: 'Copy the complete prompt text and review any optional customization notes.',
                    cue: 'Full Prompt Text · Camera & Lighting Notes',
                    icon: Clipboard,
                  },
                  {
                    num: '03',
                    title: 'Paste & Tailor Details',
                    desc: 'Paste it into a compatible image-generation tool and adjust the subject, composition, lighting, or aspect ratio when necessary.',
                    cue: 'Subject · Composition · Lighting · Aspect Ratio',
                    icon: SlidersHorizontal,
                  },
                ].map((step) => {
                  const StepIcon = step.icon;
                  return (
                    <li
                      key={step.num}
                      className="bg-white border border-[#E4E2F5] rounded-2xl p-5 flex flex-col"
                    >
                      <div className="flex items-center justify-between gap-3 mb-3.5">
                        <span className="inline-flex items-center justify-center h-9 px-2.5 rounded-lg bg-[#0F172A] text-white font-display text-xs font-extrabold">
                          {step.num}
                        </span>
                        <span className="w-9 h-9 rounded-lg bg-[#F2EFFF] text-[#5B3DF5] inline-flex items-center justify-center">
                          <StepIcon className="w-4 h-4" />
                        </span>
                      </div>
                      <h3 className="font-display font-extrabold text-base text-[#0F172A] mb-1.5">{step.title}</h3>
                      <p className="text-xs text-slate-600 leading-relaxed flex-1 mb-3">{step.desc}</p>
                      <span className="pt-2.5 border-t border-[#F1F0FA] text-[11px] font-semibold text-slate-500">
                        {step.cue}
                      </span>
                    </li>
                  );
                })}
              </ol>
            </div>

            <div className="mt-5 bg-white border border-[#DEDBF5] rounded-xl p-4 flex items-start gap-3">
              <span className="w-8 h-8 rounded-lg bg-[#F2EFFF] text-[#5B3DF5] inline-flex items-center justify-center shrink-0 mt-0.5">
                <Info className="w-4 h-4" />
              </span>
              <p className="text-xs text-slate-700 leading-relaxed">
                <strong className="text-[#0F172A]">Cross-Tool Interpretation Note:</strong> Different AI tools interpret the same prompt wording differently. Treat each prompt as a structured starting point and refine lighting, framing, or aspect ratio inside your chosen generator rather than expecting identical outputs.
              </p>
            </div>
          </div>
        </section>

        {/* Section 4 (v2.4.0): Why Choose PromptSyra? Asymmetric Editorial Showcase */}
        <section className="py-9 px-5">
          <div className="max-w-[1160px] mx-auto">
            <div className="mb-7">
              <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">Editorial Purpose &amp; Approach</p>
              <h2 className="font-display font-extrabold text-2xl text-[#0F172A]">Why Choose PromptSyra?</h2>
              <p className="text-sm text-slate-600 max-w-2xl mt-2 leading-relaxed">
                Writing descriptive photo prompts from scratch often takes repeated trial and error. PromptSyra organizes clear photographic phrasing and practical adaptation notes so you start with a dependable structure.
              </p>
            </div>

            <div className="space-y-5">
              {/* Area 1: Clear, Copy-Ready Prompt Formatting + Working Copy Sample Specimen */}
              <article
                className={`p-6 md:p-7 rounded-2xl border border-[#E5E7F3] bg-white grid gap-7 items-center ${
                  viewport === 'desktop' ? 'grid-cols-12' : 'grid-cols-1'
                }`}
              >
                <div className={viewport === 'desktop' ? 'col-span-5' : ''}>
                  <span className="block font-mono text-[11px] font-bold uppercase tracking-wider text-[#5B3DF5] mb-1.5">
                    01 / Structured Phrasing
                  </span>
                  <h3 className="font-display font-extrabold text-xl text-[#0F172A] mb-2.5">
                    Clear, Copy-Ready Prompt Formatting
                  </h3>
                  <p className="text-xs text-slate-600 leading-relaxed mb-4">
                    Instead of cluttered comma-stuffed keyword lists, prompts on PromptSyra are written in clean, readable English—separating subject description, camera perspective, lighting direction, and backdrop mood so you can copy them directly or edit any clause in seconds.
                  </p>
                  <ul className="space-y-2 text-xs font-medium text-slate-800">
                    <li className="flex items-start gap-2">
                      <Check className="w-3.5 h-3.5 text-[#5B3DF5] shrink-0 mt-0.5" />
                      <span>Plain-English clauses organized in logical photographic order</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <Check className="w-3.5 h-3.5 text-[#5B3DF5] shrink-0 mt-0.5" />
                      <span>Easy to spot which words control lens depth, light, and background</span>
                    </li>
                    <li className="flex items-start gap-2">
                      <Check className="w-3.5 h-3.5 text-[#5B3DF5] shrink-0 mt-0.5" />
                      <span>Clean one-click copy formatting without stray labels or numbering</span>
                    </li>
                  </ul>
                </div>

                <div className={viewport === 'desktop' ? 'col-span-7' : ''}>
                  <div className="bg-[#F8F9FC] border border-[#DFE3F0] rounded-2xl overflow-hidden">
                    <div className="px-4 py-3 bg-[#0F172A] text-white flex flex-wrap items-center justify-between gap-3">
                      <div>
                        <span className="block text-[10px] font-semibold uppercase tracking-wider text-indigo-300">
                          Anatomical Prompt Example
                        </span>
                        <strong className="font-display text-xs font-bold text-white">
                          85mm Natural Window Portrait
                        </strong>
                      </div>
                      <button
                        type="button"
                        onClick={() => void handleCopySamplePrompt()}
                        className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-display font-bold text-white transition-colors whitespace-nowrap ${
                          sampleCopied
                            ? 'bg-emerald-600'
                            : 'bg-[#5B3DF5] hover:bg-[#492CD9] border border-white/20'
                        }`}
                      >
                        {sampleCopied ? (
                          <>
                            <Check className="w-3.5 h-3.5" />
                            <span>Copied Sample</span>
                          </>
                        ) : (
                          <>
                            <Clipboard className="w-3.5 h-3.5" />
                            <span>Copy Sample Prompt</span>
                          </>
                        )}
                      </button>
                      <span className="sr-only" aria-live="polite">
                        {sampleCopied ? 'Sample prompt copied to clipboard.' : ''}
                      </span>
                    </div>

                    <dl className="px-4 py-1.5 divide-y divide-[#E6E9F4] text-xs">
                      {[
                        {
                          key: 'Subject',
                          val: 'Editorial three-quarter portrait of a young woman wearing a warm oatmeal knit sweater,',
                        },
                        {
                          key: 'Framing',
                          val: 'captured from eye level with an 85mm prime lens and shallow f/1.8 depth of field,',
                        },
                        {
                          key: 'Lighting',
                          val: 'illuminated by soft directional north-window daylight with gentle shadow roll-off,',
                        },
                        {
                          key: 'Backdrop',
                          val: 'set against a muted warmstone studio backdrop with natural skin texture and balanced filmic color tones.',
                        },
                      ].map((row) => (
                        <div
                          key={row.key}
                          className={`py-2.5 grid gap-2 ${
                            viewport === 'mobile' ? 'grid-cols-1' : 'grid-cols-[84px_1fr] items-baseline'
                          }`}
                        >
                          <dt className="font-mono text-[11px] font-bold uppercase tracking-wider text-[#5B3DF5]">
                            {row.key}
                          </dt>
                          <dd className="text-slate-800 leading-relaxed">{row.val}</dd>
                        </div>
                      ))}
                    </dl>
                  </div>
                </div>
              </article>

              {/* Areas 2 & 3: Photo Styles Matrix + 5-Factor Customization Ledger */}
              <div
                className={`grid gap-5 ${
                  viewport === 'desktop' ? 'grid-cols-2' : 'grid-cols-1'
                }`}
              >
                {/* Area 2: Ideas for Different Photo Styles */}
                <article className="p-6 rounded-2xl border border-[#E5E7F3] bg-white flex flex-col">
                  <span className="block font-mono text-[11px] font-bold uppercase tracking-wider text-[#5B3DF5] mb-1.5">
                    02 / Visual Variety
                  </span>
                  <h3 className="font-display font-extrabold text-lg text-[#0F172A] mb-2">
                    Ideas for Different Photo Styles
                  </h3>
                  <p className="text-xs text-slate-600 leading-relaxed mb-4">
                    Browse distinct photographic directions spanning everyday studio portraits, conceptual compositions, seasonal themes, and vertical social layouts.
                  </p>

                  <div
                    className={`grid gap-3 mt-auto ${
                      viewport === 'mobile' ? 'grid-cols-1' : 'grid-cols-2'
                    }`}
                  >
                    {[
                      {
                        title: 'Portrait Photography',
                        note: 'Natural window light, studio headshots & 85mm depth',
                        icon: User,
                      },
                      {
                        title: 'Creative Compositions',
                        note: 'Editorial color palettes, textures & styled studio sets',
                        icon: Layers,
                      },
                      {
                        title: 'Seasonal Concepts',
                        note: 'Festive attire, golden-hour outdoor & cultural themes',
                        icon: Sun,
                      },
                      {
                        title: 'Social Media Visuals',
                        note: '9:16 stories, reel covers & clean thumbnail framing',
                        icon: Smartphone,
                      },
                    ].map((styleItem) => {
                      const StyleIcon = styleItem.icon;
                      return (
                        <div
                          key={styleItem.title}
                          className="p-3.5 rounded-xl bg-[#F9F8FF] border border-[#E7E5F8] flex items-start gap-2.5"
                        >
                          <span className="w-8 h-8 rounded-lg bg-white border border-[#E2DFF6] text-[#5B3DF5] inline-flex items-center justify-center shrink-0">
                            <StyleIcon className="w-4 h-4" />
                          </span>
                          <div>
                            <h4 className="font-display font-bold text-xs text-[#0F172A] mb-0.5">
                              {styleItem.title}
                            </h4>
                            <p className="text-[11px] text-slate-600 leading-snug">{styleItem.note}</p>
                          </div>
                        </div>
                      );
                    })}
                  </div>
                </article>

                {/* Area 3: Helpful Customization Guidance */}
                <article className="p-6 rounded-2xl border border-[#E5E7F3] bg-white flex flex-col">
                  <span className="block font-mono text-[11px] font-bold uppercase tracking-wider text-[#5B3DF5] mb-1.5">
                    03 / Adaptable Variables
                  </span>
                  <h3 className="font-display font-extrabold text-lg text-[#0F172A] mb-2">
                    Helpful Customization Guidance
                  </h3>
                  <p className="text-xs text-slate-600 leading-relaxed mb-3.5">
                    Rather than locking you into rigid templates, articles highlight the key variables you can swap to tailor a scene to your own concept:
                  </p>

                  <dl className="border-t border-[#ECEEF6] divide-y divide-[#ECEEF6] text-xs mb-4">
                    {[
                      {
                        factor: 'Subject details',
                        guide: 'Wardrobe colors, pose, expression, or everyday props',
                      },
                      {
                        factor: 'Lighting',
                        guide: 'Soft window daylight, studio rim light, or warm dusk glow',
                      },
                      {
                        factor: 'Background',
                        guide: 'Neutral studio canvas, outdoor street depth, or interior room',
                      },
                      {
                        factor: 'Framing',
                        guide: 'Close-up portrait crop, waist-up editorial, or wide angle',
                      },
                      {
                        factor: 'Aspect ratio',
                        guide: '4:5 feed portrait, 9:16 mobile story, or 16:9 widescreen',
                      },
                    ].map((item) => (
                      <div
                        key={item.factor}
                        className={`py-2 grid gap-2 ${
                          viewport === 'mobile' ? 'grid-cols-1' : 'grid-cols-[118px_1fr] items-baseline'
                        }`}
                      >
                        <dt className="font-display font-bold text-[#0F172A]">{item.factor}</dt>
                        <dd className="text-slate-600 leading-snug">{item.guide}</dd>
                      </div>
                    ))}
                  </dl>

                  <p className="mt-auto p-3 rounded-xl bg-[#F6F5FF] border border-[#E3E0F7] text-[11px] text-slate-700 leading-relaxed">
                    <strong className="text-[#0F172A]">Practical Tip:</strong> Adjust one or two variables at a time so lighting and composition stay balanced as your generator interprets the updated prompt.
                  </p>
                </article>
              </div>
            </div>
          </div>
        </section>

        {/* G. Community & Social Channels (Strictly Conditional — Hidden Unless Official URLs Exist) */}
        {simulateOfficialSocialsConfigured && (
          <section className="py-8 px-5">
            <div className="max-w-[1160px] mx-auto">
              <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">PromptSyra.com Channels</p>
              <h2 className="font-display font-extrabold text-2xl text-[#0F172A] mb-5">Connect With PromptSyra</h2>
              <div
                className={`grid gap-4 ${
                  viewport === 'mobile' ? 'grid-cols-1' : 'grid-cols-3'
                }`}
              >
                {[
                  {
                    name: 'Instagram',
                    desc: 'Browse visual portrait studies, lighting references, and featured prompt previews.',
                  },
                  {
                    name: 'YouTube',
                    desc: 'Watch step-by-step prompt customization walkthroughs and visual comparisons.',
                  },
                  {
                    name: 'Facebook',
                    desc: 'Follow new article updates, photography prompt collections, and community discussions.',
                  },
                ].map((ch) => (
                  <div key={ch.name} className="p-5 rounded-2xl border border-[#E5E7F3] bg-white">
                    <h3 className="font-display font-bold text-base text-[#0F172A] mb-1.5">{ch.name}</h3>
                    <p className="text-xs text-slate-600 leading-relaxed mb-3">{ch.desc}</p>
                    <span className="inline-flex items-center gap-1 text-xs font-semibold text-[#5B3DF5]">
                      <span>Configured via Customizer</span>
                      <ArrowRight className="w-3.5 h-3.5" />
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </section>
        )}

        {/* H. Frequently Asked Questions + I. Designated Authors Feature */}
        <section className="py-10 px-5 bg-[#F6F5FF] border-t border-[#E5E7F3]">
          <div
            className={`max-w-[1160px] mx-auto grid gap-8 items-start ${
              viewport === 'desktop' ? 'grid-cols-12' : 'grid-cols-1'
            }`}
          >
            {/* Accessible FAQ Accordion */}
            <div className={viewport === 'desktop' ? 'col-span-7' : ''}>
              <p className="font-display text-xs font-bold text-[#5B3DF5] mb-1">Helpful Answers</p>
              <h2 className="font-display font-extrabold text-2xl text-[#0F172A] mb-4">
                Frequently Asked Questions
              </h2>

              <div className="space-y-2.5">
                {DEFAULT_FAQS.map((item, idx) => {
                  const isOpen = openFaqIdx === idx;
                  return (
                    <div key={item.q} className="bg-white border border-[#E5E7F3] rounded-xl">
                      <button
                        type="button"
                        aria-expanded={isOpen}
                        onClick={() => setOpenFaqIdx(isOpen ? null : idx)}
                        className="w-full px-4 py-3.5 flex items-center justify-between gap-4 text-left font-display font-bold text-sm text-[#0F172A]"
                      >
                        <span>{item.q}</span>
                        {isOpen ? (
                          <Minus className="w-4 h-4 text-[#5B3DF5] shrink-0" />
                        ) : (
                          <Plus className="w-4 h-4 text-[#5B3DF5] shrink-0" />
                        )}
                      </button>
                      {isOpen && (
                        <div className="px-4 pb-4 text-xs text-slate-600 leading-relaxed">{item.a}</div>
                      )}
                    </div>
                  );
                })}
              </div>
            </div>

            {/* Syra Ansari & 3 Designated Editorial Profiles */}
            <aside className={viewport === 'desktop' ? 'col-span-5' : ''}>
              <div className="bg-white border border-[#E5E7F3] rounded-2xl p-6">
                <p className="font-display text-xs font-bold text-[#5B3DF5] mb-3">Editorial Feature</p>
                <div className="flex items-center gap-3.5 mb-4">
                  <div className="w-14 h-14 rounded-full bg-[#EEECFF] text-[#5B3DF5] font-display font-extrabold text-lg inline-flex items-center justify-center shrink-0">
                    SA
                  </div>
                  <div>
                    <h3 className="font-display font-extrabold text-lg text-[#0F172A]">Syra Ansari</h3>
                    <p className="text-xs text-slate-500">Lead Prompt Editor · PromptSyra.com</p>
                  </div>
                </div>

                <p className="text-xs text-slate-600 leading-relaxed mb-3">
                  Reads directly from Syra Ansari&apos;s real WordPress user biography and portrait field without fabricating credentials or overwriting existing article authorship.
                </p>

                {simulateAdminLoggedIn && (
                  <p className="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-4">
                    Admin-Only Flag: Populate real Biographical Info &amp; Portrait URL under WordPress Users → Profile.
                  </p>
                )}

                <button
                  type="button"
                  onClick={() => handleSimulatedNav('Syra Ansari Author Archive', '/author/syra-ansari/')}
                  className="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#5B3DF5] hover:bg-[#492CD9] text-white font-display text-xs font-semibold transition-colors"
                >
                  <span>View Articles by Syra Ansari</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </button>

                <div className="mt-5 pt-4 border-t border-[#E5E7F3]">
                  <h4 className="font-display text-xs font-bold text-slate-500 mb-2.5">
                    Designated Editorial Profiles (3)
                  </h4>
                  <ul className="space-y-2 text-xs">
                    {[
                      { name: 'Syra Ansari', slug: 'syra-ansari' },
                      { name: 'Arjun Patel', slug: 'arjun-patel' },
                      { name: 'Neha Joshi', slug: 'neha-joshi' },
                    ].map((author) => (
                      <li key={author.slug} className="flex items-center justify-between">
                        <span className="font-semibold text-[#0F172A]">{author.name}</span>
                        <button
                          type="button"
                          onClick={() =>
                            handleSimulatedNav(`${author.name} Archive`, `/author/${author.slug}/`)
                          }
                          className="font-semibold text-[#5B3DF5] hover:underline"
                        >
                          /author/{author.slug}/ →
                        </button>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </aside>
          </div>
        </section>

        {/* J. Footer */}
        <footer className="bg-[#0B1026] text-slate-300 pt-11 pb-7 px-5">
          <div className="max-w-[1160px] mx-auto">
            <div
              className={`grid gap-8 pb-8 border-b border-white/10 ${
                viewport === 'mobile'
                  ? 'grid-cols-1'
                  : viewport === 'tablet'
                    ? 'grid-cols-2'
                    : 'grid-cols-4'
              }`}
            >
              <div>
                <div className="inline-flex items-center gap-2 text-white font-display font-extrabold text-lg">
                  <span className="w-7 h-7 rounded-md bg-[#5B3DF5] text-white text-sm inline-flex items-center justify-center">
                    P
                  </span>
                  <span>
                    Prompt<span className="text-violet-400">Syra</span>.com
                  </span>
                </div>
                <p className="text-xs text-slate-400 mt-3 leading-relaxed">
                  PromptSyra.com curates copy-ready AI photo prompts, portrait lighting setups, and practical customization notes for visual creators.
                </p>
              </div>

              <div>
                <h3 className="font-display font-bold text-xs text-white mb-3">Navigation</h3>
                <ul className="space-y-2 text-xs text-slate-400">
                  <li>
                    <button type="button" onClick={() => handleSimulatedNav('Home', '/')} className="hover:text-white">
                      Home
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('About', '/about/')}
                      className="hover:text-white"
                    >
                      About
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Contact', '/contact/')}
                      className="hover:text-white"
                    >
                      Contact
                    </button>
                  </li>
                </ul>
              </div>

              <div>
                <h3 className="font-display font-bold text-xs text-white mb-3">Main Categories</h3>
                <ul className="space-y-2 text-xs text-slate-400">
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('AI Photo Prompts', '/category/ai-photo-prompts/')}
                      className="hover:text-white"
                    >
                      AI Photo Prompts
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Trending Prompts', '/category/trending-prompts/')}
                      className="hover:text-white"
                    >
                      Trending Prompts
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Social Media Prompts', '/category/social-media-prompts/')}
                      className="hover:text-white"
                    >
                      Social Media Prompts
                    </button>
                  </li>
                </ul>
              </div>

              <div>
                <h3 className="font-display font-bold text-xs text-white mb-3">Legal &amp; Policies</h3>
                <ul className="space-y-2 text-xs text-slate-400">
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Privacy Policy', '/privacy-policy/')}
                      className="hover:text-white"
                    >
                      Privacy Policy
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Terms and Conditions', '/terms-and-conditions/')}
                      className="hover:text-white"
                    >
                      Terms and Conditions
                    </button>
                  </li>
                  <li>
                    <button
                      type="button"
                      onClick={() => handleSimulatedNav('Disclaimer', '/disclaimer/')}
                      className="hover:text-white"
                    >
                      Disclaimer
                    </button>
                  </li>
                </ul>
              </div>
            </div>

            <div className="pt-5 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
              <p>&copy; 2026 PromptSyra.com. All rights reserved.</p>
              <p>Single-Post Template (single.php): Protected &amp; Unchanged</p>
            </div>
          </div>
        </footer>
      </div>
    </div>
  );
};
