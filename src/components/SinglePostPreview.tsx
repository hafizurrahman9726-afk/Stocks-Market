import React, { useState } from 'react';
import {
  Calendar,
  Check,
  ChevronRight,
  Clock,
  Copy,
  ExternalLink,
  Eye,
  EyeOff,
  List,
  Loader2,
  Monitor,
  Smartphone,
  Sparkles,
  Tablet,
} from 'lucide-react';

type ViewportSize = 'desktop' | 'tablet' | 'mobile';
type PreviewPostMode = 'hello-world' | 'prompt-guide';

interface SamplePromptCard {
  id: string;
  sectionHeading: string;
  intro: string;
  specs: Array<{ label: string; value: string }>;
  promptText: string;
  tip: string;
}

interface PreviewCommentItem {
  id: string;
  author: string;
  authorUrl?: string;
  date: string;
  body: string;
}

const SAMPLE_PROMPT_CARDS: SamplePromptCard[] = [
  {
    id: 'macro-reflection',
    sectionHeading: '1. Golden-Hour Macro Iris Portrait Setup',
    intro:
      'Macro portrait prompts perform best when optical focal length, catchlight direction, and natural skin micro-texture are specified explicitly rather than relying on vague quality buzzwords.',
    specs: [
      { label: 'Recommended Lens', value: '100mm f/2.8 Macro Prime' },
      { label: 'Lighting Direction', value: 'Warm golden-hour side key light (35° angle)' },
      { label: 'Aspect Ratio', value: '4:5 Vertical Portrait or 16:9 Editorial Crop' },
      { label: 'Compatible Tools', value: 'ChatGPT Image, Gemini, Grok' },
    ],
    promptText:
      'Create an ultra-realistic macro close-up photograph of a human eye illuminated by warm golden-hour sunlight. Capture intricate amber and deep hazel iris fibers, subtle natural catchlights reflecting a sunset horizon, realistic eyelash detail, and authentic skin texture around the eyelids. Shot on a 100mm f/2.8 macro lens, shallow depth of field, natural color grading, zero artificial smoothing.',
    tip: 'Replace "amber and deep hazel" with your preferred iris palette and adjust the catchlight reflection (window light, city neon, or overcast sky) to change mood.',
  },
  {
    id: 'studio-editorial',
    sectionHeading: '2. Crisp Studio Dual-Catchlight Close-Up',
    intro:
      'For clean editorial beauty or commercial framing, pair a diffused octagonal softbox with a subtle rim reflector to keep iris contrast sharp without harsh specular clipping.',
    specs: [
      { label: 'Recommended Lens', value: '85mm–105mm Macro, f/4.0' },
      { label: 'Lighting Setup', value: 'Diffused octagonal softbox + silver bounce fill' },
      { label: 'Color Profile', value: 'Neutral studio daylight (5400K)' },
      { label: 'Compatible Tools', value: 'ChatGPT Image, Gemini, Grok' },
    ],
    promptText:
      'Generate a high-detail editorial studio close-up portrait focusing on crisp eye detail under soft 5400K diffused studio lighting. Show clean octagonal softbox catchlight in the pupil, finely defined iris radial patterns, natural tear-line sheen, and true-to-life skin pores without airbrushing. Captured at 105mm f/4, tack-sharp focus plane on the iris, balanced studio contrast.',
    tip: 'Stepping the virtual aperture from f/2.8 to f/4 keeps both the iris texture and upper eyelashes crisp in frame.',
  },
];

export const SinglePostPreview: React.FC = () => {
  const [viewport, setViewport] = useState<ViewportSize>('desktop');
  const [postMode, setPostMode] = useState<PreviewPostMode>('hello-world');
  const [tocOpen, setTocOpen] = useState(true);
  const [expandedCards, setExpandedCards] = useState<Record<string, boolean>>({});
  const [copiedCardId, setCopiedCardId] = useState<string | null>(null);

  // Interactive preview state for the redesigned "Join the Conversation" WordPress comment form
  const [comments, setComments] = useState<PreviewCommentItem[]>([
    {
      id: 'comment-1',
      author: 'A WordPress Commenter',
      authorUrl: 'https://wordpress.org/',
      date: 'October 27, 2026 at 7:26 am',
      body: 'Hi, this is a comment. To get started with moderating, editing, and deleting comments, please visit the Comments screen in the dashboard.',
    },
  ]);
  const [commentBody, setCommentBody] = useState('');
  const [authorName, setAuthorName] = useState('');
  const [authorEmail, setAuthorEmail] = useState('');
  const [authorWebsite, setAuthorWebsite] = useState('');
  const [saveCookies, setSaveCookies] = useState(false);
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});
  const [isSubmittingComment, setIsSubmittingComment] = useState(false);

  const toggleExpand = (id: string) => {
    setExpandedCards((prev) => ({ ...prev, [id]: !prev[id] }));
  };

  const handleCopyPrompt = async (id: string, cleanPromptText: string) => {
    try {
      await navigator.clipboard.writeText(cleanPromptText);
    } catch {
      const ta = document.createElement('textarea');
      ta.value = cleanPromptText;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
    }
    setCopiedCardId(id);
    setTimeout(() => {
      setCopiedCardId((curr) => (curr === id ? null : curr));
    }, 2200);
  };

  const handleCommentSubmit = (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const nextErrors: Record<string, string> = {};

    if (!commentBody.trim()) {
      nextErrors.comment = 'Please enter your comment before submitting.';
    }
    if (!authorName.trim()) {
      nextErrors.author = 'Please enter your name.';
    }
    if (!authorEmail.trim()) {
      nextErrors.email = 'Please enter your email address.';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(authorEmail.trim())) {
      nextErrors.email = 'Please enter a valid email address (for example, name@example.com).';
    }
    if (authorWebsite.trim() && !/^https?:\/\/.+/i.test(authorWebsite.trim())) {
      nextErrors.url =
        'Please enter a valid URL starting with https:// or http:// (or leave optional field blank).';
    }

    setFieldErrors(nextErrors);
    if (Object.keys(nextErrors).length > 0) {
      return;
    }

    setIsSubmittingComment(true);
    setTimeout(() => {
      setComments((prev) => [
        ...prev,
        {
          id: `comment-${Date.now()}`,
          author: authorName.trim(),
          authorUrl: authorWebsite.trim() || undefined,
          date: 'Just now (Simulated WP Comment Submission)',
          body: commentBody.trim(),
        },
      ]);
      setCommentBody('');
      if (!saveCookies) {
        setAuthorName('');
        setAuthorEmail('');
        setAuthorWebsite('');
      }
      setIsSubmittingComment(false);
    }, 450);
  };

  const containerMaxWidth =
    viewport === 'mobile'
      ? 'max-w-[390px]'
      : viewport === 'tablet'
        ? 'max-w-[768px]'
        : 'max-w-full';

  const isDesktopGrid = viewport === 'desktop';

  return (
    <div className="space-y-4">
      {/* Preview Controls Bar */}
      <div className="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div className="flex flex-wrap items-center gap-2">
          <span className="text-xs font-semibold text-slate-500 mr-1">Simulated Post:</span>
          <button
            type="button"
            onClick={() => setPostMode('hello-world')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              postMode === 'hello-world'
                ? 'bg-violet-600 text-white'
                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
            }`}
          >
            Target URL: /hello-world/ (v2.2.0 Final UI)
          </button>
          <button
            type="button"
            onClick={() => setPostMode('prompt-guide')}
            className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors ${
              postMode === 'prompt-guide'
                ? 'bg-violet-600 text-white'
                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'
            }`}
          >
            Full Prompt Article (Tables + Copy Cards + Auto-TOC)
          </button>
        </div>

        <div className="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
          {(
            [
              { id: 'desktop', label: 'Desktop', icon: Monitor },
              { id: 'tablet', label: 'Tablet', icon: Tablet },
              { id: 'mobile', label: 'Mobile', icon: Smartphone },
            ] as const
          ).map((item) => {
            const Icon = item.icon;
            return (
              <button
                key={item.id}
                type="button"
                onClick={() => setViewport(item.id)}
                className={`inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-xs font-semibold transition-colors ${
                  viewport === item.id
                    ? 'bg-white text-slate-900 shadow-xs'
                    : 'text-slate-600 hover:text-slate-900'
                }`}
              >
                <Icon className="w-3.5 h-3.5" />
                <span>{item.label}</span>
              </button>
            );
          })}
        </div>
      </div>

      {/* Simulated WordPress Single-Post Viewport */}
      <div
        className={`${containerMaxWidth} mx-auto bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs transition-all`}
      >
        {/* PromptSyra Site Header (Category Navigation Preserved in Header!) */}
        <div className="border-b border-slate-200 bg-white px-5 py-3.5 flex items-center justify-between gap-4">
          <div className="flex items-center gap-2.5">
            <span className="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-600 to-indigo-600 text-white font-display font-extrabold text-sm flex items-center justify-center">
              P
            </span>
            <div className="leading-tight">
              <span className="font-display font-extrabold text-sm text-slate-900">
                Prompt<span className="text-violet-600">Syra</span>.com
              </span>
              <span className="block text-[10px] text-slate-500">Smart Prompts · Better Creations</span>
            </div>
          </div>

          {viewport === 'desktop' && (
            <div className="flex items-center gap-5 text-xs font-semibold text-slate-600">
              <span>Home</span>
              <span>AI Prompts ▾</span>
              <span>Categories ▾</span>
              <span>About</span>
              <span>Contact</span>
            </div>
          )}

          <span className="px-3 py-1.5 rounded-full bg-violet-600 text-white text-[11px] font-semibold whitespace-nowrap">
            Explore Prompts →
          </span>
        </div>

        {/* 2-Column Single-Post Grid (Breadcrumb Row & Standalone Category Badge Cleanly Removed — Zero Blank Spacing!) */}
        <div
          className={`max-w-[1180px] mx-auto px-5 py-8 ${
            isDesktopGrid
              ? 'grid grid-cols-[minmax(0,1fr)_320px] gap-9 items-start'
              : 'flex flex-col gap-8'
          }`}
        >
          {/* Left Main Article Column */}
          <div className="min-w-0">
            <article>
              {/* H1 Article Title Starts Cleanly at Top */}
              <h1 className="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight leading-tight mb-4">
                {postMode === 'hello-world'
                  ? 'Hello world!'
                  : 'Macro Iris & Studio Portrait AI Photo Prompts (Copy-Ready Guide)'}
              </h1>

              {/* Author + Published Date + Reading Time Meta Bar */}
              <div className="flex flex-wrap items-center justify-between gap-3 py-3.5 mb-6 border-y border-slate-200">
                <div className="flex items-center gap-3">
                  <span className="w-10 h-10 rounded-full bg-gradient-to-br from-violet-600 to-indigo-600 text-white font-display font-bold text-xs flex items-center justify-center border-2 border-violet-500/25 shrink-0">
                    {postMode === 'hello-world' ? 'PR' : 'SA'}
                  </span>
                  <div className="leading-tight">
                    <p className="text-xs text-slate-600">
                      By{' '}
                      <span className="font-bold text-slate-900">
                        {postMode === 'hello-world' ? 'promptsyra' : 'Syra Ansari'}
                      </span>
                    </p>
                    <span className="text-[11px] font-medium text-slate-500">Author</span>
                  </div>
                </div>

                <div className="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                  <span className="inline-flex items-center gap-1.5">
                    <Calendar className="w-3.5 h-3.5 text-violet-600" />
                    <span>
                      Published:{' '}
                      <strong className="text-slate-700 font-semibold">October 27, 2026</strong>
                    </span>
                  </span>
                  <span className="text-slate-300">•</span>
                  <span className="inline-flex items-center gap-1.5">
                    <Clock className="w-3.5 h-3.5 text-violet-600" />
                    <span>
                      Read time:{' '}
                      <strong className="text-slate-700 font-semibold">
                        {postMode === 'hello-world' ? '1 min read' : '4 min read'}
                      </strong>
                    </span>
                  </span>
                </div>
              </div>

              {/* Dynamic Post Content */}
              {postMode === 'hello-world' ? (
                <div className="space-y-4 text-[16.5px] leading-relaxed text-slate-700">
                  <p>
                    Welcome to WordPress. This is your first post. Edit or delete it, then start
                    writing!
                  </p>
                </div>
              ) : (
                <div className="space-y-6 text-[16px] leading-relaxed text-slate-700">
                  <p>
                    Detailed close-up portrait prompts work best when camera optics, lighting angle,
                    and natural texture rules are structured clearly.
                  </p>

                  {/* Conditional Auto Table of Contents */}
                  <nav
                    aria-label="Table of Contents"
                    className="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-5"
                  >
                    <div className="flex items-center justify-between gap-3">
                      <span className="inline-flex items-center gap-2 font-display font-extrabold text-sm text-slate-900">
                        <List className="w-4 h-4 text-violet-600" />
                        <span>Table of Contents</span>
                      </span>
                      <button
                        type="button"
                        onClick={() => setTocOpen((v) => !v)}
                        className="px-2.5 py-1 rounded-full border border-slate-300 bg-white text-[11px] font-bold text-slate-600 hover:border-violet-600 hover:text-violet-700 transition-colors"
                      >
                        {tocOpen ? 'Hide' : 'Show'}
                      </button>
                    </div>

                    {tocOpen && (
                      <ul className="mt-3.5 pt-3.5 border-t border-slate-200 space-y-2 text-xs sm:text-sm">
                        <li>
                          <a
                            href="#preview-sec-1"
                            onClick={(e) => e.preventDefault()}
                            className="inline-flex items-baseline gap-2 text-slate-700 hover:text-violet-700 font-medium"
                          >
                            <span className="font-display font-bold text-xs text-violet-600">1</span>
                            <span>Golden-Hour Macro Iris Portrait Setup</span>
                          </a>
                        </li>
                        <li>
                          <a
                            href="#preview-sec-2"
                            onClick={(e) => e.preventDefault()}
                            className="inline-flex items-baseline gap-2 text-slate-700 hover:text-violet-700 font-medium"
                          >
                            <span className="font-display font-bold text-xs text-violet-600">2</span>
                            <span>Crisp Studio Dual-Catchlight Close-Up</span>
                          </a>
                        </li>
                      </ul>
                    )}
                  </nav>

                  {SAMPLE_PROMPT_CARDS.map((card, index) => {
                    const isExpanded = !!expandedCards[card.id];
                    const isCopied = copiedCardId === card.id;
                    const encoded = encodeURIComponent(card.promptText);

                    return (
                      <section key={card.id} id={`preview-sec-${index + 1}`} className="space-y-4 pt-2">
                        <h2 className="font-display font-extrabold text-xl sm:text-2xl text-slate-900 tracking-tight">
                          {card.sectionHeading}
                        </h2>
                        <p>{card.intro}</p>

                        {/* Responsive Specification Table */}
                        <div className="overflow-x-auto border border-slate-200 rounded-xl bg-white">
                          <table className="w-full min-w-[420px] text-left border-collapse text-xs sm:text-sm">
                            <thead>
                              <tr className="bg-slate-50 border-b border-slate-200">
                                <th className="py-2.5 px-4 font-display font-bold text-slate-900 w-2/5">
                                  Parameter
                                </th>
                                <th className="py-2.5 px-4 font-display font-bold text-slate-900">
                                  Recommended Setting
                                </th>
                              </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                              {card.specs.map((row) => (
                                <tr key={row.label}>
                                  <td className="py-2.5 px-4 font-bold text-slate-900 bg-slate-50/60">
                                    {row.label}
                                  </td>
                                  <td className="py-2.5 px-4 text-slate-700">{row.value}</td>
                                </tr>
                              ))}
                            </tbody>
                          </table>
                        </div>

                        {/* Interactive AI Prompt Box */}
                        <div className="bg-white border border-slate-200 hover:border-slate-300 rounded-2xl p-5 shadow-xs transition-all">
                          <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-violet-50 border border-violet-500/20 text-violet-700 font-display font-bold text-[11px] uppercase tracking-wider mb-3">
                            <Sparkles className="w-3.5 h-3.5" />
                            <span>AI Photo Prompt</span>
                          </div>

                          <div
                            className={`relative overflow-hidden transition-all duration-300 ${
                              isExpanded ? 'max-h-[900px]' : 'max-h-[84px]'
                            }`}
                          >
                            <p className="text-[15px] leading-relaxed text-slate-700 m-0 pr-1">
                              {card.promptText}
                            </p>
                            {!isExpanded && (
                              <div
                                aria-hidden="true"
                                className="absolute inset-x-0 bottom-0 h-10 bg-gradient-to-b from-white/0 to-white pointer-events-none"
                              />
                            )}
                          </div>

                          <div className="mt-4 pt-4 border-t border-dashed border-slate-200 flex flex-wrap items-center gap-2.5">
                            <button
                              type="button"
                              aria-expanded={isExpanded}
                              onClick={() => toggleExpand(card.id)}
                              className="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full bg-[#FFD400] hover:bg-[#FACC15] border border-[#E0B800] text-[#111111] font-display font-bold text-xs transition-all cursor-pointer"
                            >
                              {isExpanded ? (
                                <>
                                  <EyeOff className="w-3.5 h-3.5" />
                                  <span>Collapse Prompt</span>
                                </>
                              ) : (
                                <>
                                  <Eye className="w-3.5 h-3.5" />
                                  <span>View Prompt</span>
                                </>
                              )}
                            </button>

                            <button
                              type="button"
                              onClick={() => void handleCopyPrompt(card.id, card.promptText)}
                              className={`inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full font-display font-bold text-xs transition-all cursor-pointer border ${
                                isCopied
                                  ? 'bg-emerald-500 border-emerald-600 text-white'
                                  : 'bg-[#FFD400] hover:bg-[#FACC15] border-[#E0B800] text-[#111111]'
                              }`}
                            >
                              {isCopied ? (
                                <>
                                  <Check className="w-3.5 h-3.5" />
                                  <span>Copied!</span>
                                </>
                              ) : (
                                <>
                                  <Copy className="w-3.5 h-3.5" />
                                  <span>Copy Prompt</span>
                                </>
                              )}
                            </button>

                            <a
                              href={`https://chatgpt.com/?q=${encoded}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-slate-50 hover:bg-violet-50 border border-slate-300 hover:border-violet-600 text-slate-800 hover:text-violet-700 text-xs font-semibold transition-colors"
                            >
                              <span>ChatGPT</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>

                            <a
                              href={`https://gemini.google.com/?q=${encoded}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-slate-50 hover:bg-violet-50 border border-slate-300 hover:border-violet-600 text-slate-800 hover:text-violet-700 text-xs font-semibold transition-colors"
                            >
                              <span>Gemini</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>

                            <a
                              href={`https://x.com/i/grok?q=${encoded}`}
                              target="_blank"
                              rel="noopener noreferrer"
                              className="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-slate-50 hover:bg-violet-50 border border-slate-300 hover:border-violet-600 text-slate-800 hover:text-violet-700 text-xs font-semibold transition-colors"
                            >
                              <span>Grok</span>
                              <ExternalLink className="w-3 h-3" />
                            </a>
                          </div>
                        </div>

                        <div className="bg-violet-50/80 border-l-4 border-violet-600 rounded-r-xl p-3.5 text-xs sm:text-sm text-slate-800">
                          <strong>Customization Tip:</strong> {card.tip}
                        </div>
                      </section>
                    );
                  })}
                </div>
              )}
            </article>

            {/* Redesigned Native WordPress Comments & "Join the Conversation" Form (comments.php) */}
            <section
              aria-label="Article Comments"
              className="mt-12 pt-9 border-t border-slate-200 space-y-8"
            >
              {/* Existing Comments List */}
              <div className="space-y-4">
                <h2 className="font-display font-extrabold text-lg text-slate-900 tracking-tight">
                  {comments.length} {comments.length === 1 ? 'Comment' : 'Comments'} on &ldquo;
                  {postMode === 'hello-world'
                    ? 'Hello world!'
                    : 'Macro Iris & Studio Portrait AI Photo Prompts'}
                  &rdquo;
                </h2>

                <ol className="space-y-4">
                  {comments.map((c) => (
                    <li
                      key={c.id}
                      className="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs"
                    >
                      <div className="flex flex-wrap items-center justify-between gap-2 mb-3">
                        <div className="flex items-center gap-3">
                          <span className="w-10 h-10 rounded-full bg-violet-50 border-2 border-violet-500/20 text-violet-700 font-display font-bold text-xs flex items-center justify-center shrink-0">
                            {c.author.slice(0, 2).toUpperCase()}
                          </span>
                          <span className="font-display font-bold text-sm text-slate-900">
                            {c.author}
                          </span>
                        </div>
                        <span className="text-xs text-slate-500">{c.date}</span>
                      </div>
                      <p className="text-sm leading-relaxed text-slate-700">{c.body}</p>
                      <div className="mt-3">
                        <span className="inline-flex items-center px-3 py-1 rounded-full bg-slate-50 hover:bg-violet-50 border border-slate-200 hover:border-violet-300 text-xs font-display font-bold text-slate-600 hover:text-violet-700 cursor-pointer transition-colors">
                          Reply
                        </span>
                      </div>
                    </li>
                  ))}
                </ol>
              </div>

              {/* Redesigned "Join the Conversation" Form Card */}
              <div className="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-sm">
                <div className="pb-4 mb-5 border-b border-slate-100">
                  <h2 className="font-display font-extrabold text-xl sm:text-2xl text-slate-950 tracking-tight">
                    Join the Conversation
                  </h2>
                  <p className="text-sm sm:text-[15px] text-slate-600 leading-relaxed mt-2">
                    Have a question or suggestion? Share your thoughts with the PromptSyra
                    community.
                  </p>
                </div>

                <form noValidate onSubmit={handleCommentSubmit} className="space-y-5">
                  <p className="text-xs text-slate-500">
                    Your email address will not be published. Required fields are marked{' '}
                    <span className="text-violet-600 font-bold">*</span>
                  </p>

                  {/* Your Comment * */}
                  <div className="space-y-2">
                    <label
                      htmlFor="preview-comment"
                      className="block font-display font-bold text-xs sm:text-sm text-slate-950"
                    >
                      Your Comment <span className="text-violet-600">*</span>
                    </label>
                    <textarea
                      id="preview-comment"
                      rows={5}
                      value={commentBody}
                      aria-invalid={!!fieldErrors.comment}
                      onChange={(e) => {
                        setCommentBody(e.target.value);
                        if (fieldErrors.comment) {
                          setFieldErrors((prev) => ({ ...prev, comment: '' }));
                        }
                      }}
                      placeholder="Write your comment here..."
                      className={`w-full rounded-xl border px-4 py-3.5 text-sm sm:text-[15px] text-slate-950 placeholder:text-slate-400 bg-white min-h-[140px] transition-all focus:outline-none ${
                        fieldErrors.comment
                          ? 'border-red-600 ring-3 ring-red-600/10'
                          : 'border-slate-300 hover:border-slate-400 focus:border-violet-600 focus:ring-4 focus:ring-violet-600/12'
                      }`}
                    />
                    {fieldErrors.comment && (
                      <p role="alert" className="text-xs font-semibold text-red-600">
                        {fieldErrors.comment}
                      </p>
                    )}
                  </div>

                  {/* Name * */}
                  <div className="space-y-2">
                    <label
                      htmlFor="preview-author"
                      className="block font-display font-bold text-xs sm:text-sm text-slate-950"
                    >
                      Name <span className="text-violet-600">*</span>
                    </label>
                    <input
                      id="preview-author"
                      type="text"
                      value={authorName}
                      aria-invalid={!!fieldErrors.author}
                      onChange={(e) => {
                        setAuthorName(e.target.value);
                        if (fieldErrors.author) {
                          setFieldErrors((prev) => ({ ...prev, author: '' }));
                        }
                      }}
                      placeholder="Enter your name"
                      className={`w-full rounded-xl border px-4 py-3 text-sm sm:text-[15px] text-slate-950 placeholder:text-slate-400 bg-white transition-all focus:outline-none ${
                        fieldErrors.author
                          ? 'border-red-600 ring-3 ring-red-600/10'
                          : 'border-slate-300 hover:border-slate-400 focus:border-violet-600 focus:ring-4 focus:ring-violet-600/12'
                      }`}
                    />
                    {fieldErrors.author && (
                      <p role="alert" className="text-xs font-semibold text-red-600">
                        {fieldErrors.author}
                      </p>
                    )}
                  </div>

                  {/* Email Address * */}
                  <div className="space-y-2">
                    <label
                      htmlFor="preview-email"
                      className="block font-display font-bold text-xs sm:text-sm text-slate-950"
                    >
                      Email Address <span className="text-violet-600">*</span>
                    </label>
                    <input
                      id="preview-email"
                      type="email"
                      value={authorEmail}
                      aria-invalid={!!fieldErrors.email}
                      onChange={(e) => {
                        setAuthorEmail(e.target.value);
                        if (fieldErrors.email) {
                          setFieldErrors((prev) => ({ ...prev, email: '' }));
                        }
                      }}
                      placeholder="name@example.com"
                      className={`w-full rounded-xl border px-4 py-3 text-sm sm:text-[15px] text-slate-950 placeholder:text-slate-400 bg-white transition-all focus:outline-none ${
                        fieldErrors.email
                          ? 'border-red-600 ring-3 ring-red-600/10'
                          : 'border-slate-300 hover:border-slate-400 focus:border-violet-600 focus:ring-4 focus:ring-violet-600/12'
                      }`}
                    />
                    {fieldErrors.email && (
                      <p role="alert" className="text-xs font-semibold text-red-600">
                        {fieldErrors.email}
                      </p>
                    )}
                  </div>

                  {/* Website (Optional) */}
                  <div className="space-y-2">
                    <label
                      htmlFor="preview-url"
                      className="block font-display font-bold text-xs sm:text-sm text-slate-950"
                    >
                      Website <span className="font-normal text-slate-500">(Optional)</span>
                    </label>
                    <input
                      id="preview-url"
                      type="url"
                      value={authorWebsite}
                      aria-invalid={!!fieldErrors.url}
                      onChange={(e) => {
                        setAuthorWebsite(e.target.value);
                        if (fieldErrors.url) {
                          setFieldErrors((prev) => ({ ...prev, url: '' }));
                        }
                      }}
                      placeholder="https://yourwebsite.com"
                      className={`w-full rounded-xl border px-4 py-3 text-sm sm:text-[15px] text-slate-950 placeholder:text-slate-400 bg-white transition-all focus:outline-none ${
                        fieldErrors.url
                          ? 'border-red-600 ring-3 ring-red-600/10'
                          : 'border-slate-300 hover:border-slate-400 focus:border-violet-600 focus:ring-4 focus:ring-violet-600/12'
                      }`}
                    />
                    {fieldErrors.url && (
                      <p role="alert" className="text-xs font-semibold text-red-600">
                        {fieldErrors.url}
                      </p>
                    )}
                  </div>

                  {/* Save Details Consent Checkbox */}
                  <div className="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <input
                      id="preview-cookies-consent"
                      type="checkbox"
                      checked={saveCookies}
                      onChange={(e) => setSaveCookies(e.target.checked)}
                      className="mt-0.5 w-4 h-4 accent-violet-600 rounded cursor-pointer shrink-0"
                    />
                    <label
                      htmlFor="preview-cookies-consent"
                      className="text-xs sm:text-sm text-slate-700 leading-snug cursor-pointer select-none"
                    >
                      Save my name and email in this browser for the next time I comment.
                    </label>
                  </div>

                  {/* Violet Submit Button */}
                  <div>
                    <button
                      type="submit"
                      disabled={isSubmittingComment}
                      className="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3.5 rounded-xl bg-gradient-to-br from-violet-600 to-violet-700 hover:from-violet-700 hover:to-violet-800 active:bg-violet-900 text-white font-display font-bold text-sm shadow-md shadow-violet-600/25 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-violet-600 disabled:opacity-75 transition-all cursor-pointer"
                    >
                      {isSubmittingComment && <Loader2 className="w-4 h-4 animate-spin" />}
                      <span>{isSubmittingComment ? 'Posting Comment...' : 'Post Comment'}</span>
                    </button>
                  </div>
                </form>
              </div>
            </section>
          </div>

          {/* Right Sticky Reference Sidebar */}
          <aside className="space-y-5">
            {/* Widget 1: Post Author */}
            <div className="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
              <div className="pb-2.5 mb-4 border-b border-slate-100 relative">
                <h2 className="font-display font-extrabold text-sm text-slate-900">Post Author</h2>
                <span className="absolute left-0 -bottom-[1px] w-9 h-[3px] rounded-full bg-violet-600" />
              </div>

              <div className="text-center flex flex-col items-center">
                <span className="w-16 h-16 rounded-full bg-gradient-to-br from-violet-600 to-indigo-600 text-white font-display font-extrabold text-lg flex items-center justify-center border-2 border-violet-500/20 mb-2.5">
                  {postMode === 'hello-world' ? 'PR' : 'SA'}
                </span>
                <h3 className="font-display font-extrabold text-base text-slate-900">
                  {postMode === 'hello-world' ? 'promptsyra' : 'Syra Ansari'}
                </h3>
                <span className="mt-3.5 w-full py-2 px-4 rounded-full bg-slate-50 hover:bg-violet-600 hover:text-white border border-slate-200 text-slate-900 font-display font-bold text-xs transition-colors cursor-pointer">
                  View All Guides →
                </span>
              </div>
            </div>

            {/* Widget 2: Trending Prompts */}
            <div className="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
              <div className="pb-2.5 mb-4 border-b border-slate-100 relative">
                <h2 className="font-display font-extrabold text-sm text-slate-900">
                  Trending Prompts
                </h2>
                <span className="absolute left-0 -bottom-[1px] w-9 h-[3px] rounded-full bg-violet-600" />
              </div>

              <p className="text-xs text-slate-500 leading-relaxed">
                Dynamically queries recent published posts (<code className="font-mono">WP_Query</code>) excluding the current post ID.
              </p>
            </div>

            {/* Widget 3: Categories */}
            <div className="bg-white border border-slate-200 rounded-2xl p-5 shadow-2xs">
              <div className="pb-2.5 mb-4 border-b border-slate-100 relative">
                <h2 className="font-display font-extrabold text-sm text-slate-900">Categories</h2>
                <span className="absolute left-0 -bottom-[1px] w-9 h-[3px] rounded-full bg-violet-600" />
              </div>

              <ul className="space-y-2">
                {['AI Photo Prompts', 'Trending Prompts', 'Social Media Prompts'].map((cat) => (
                  <li key={cat}>
                    <span className="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50 hover:bg-violet-50 border border-slate-100 text-xs font-semibold text-slate-800 hover:text-violet-700 transition-colors cursor-pointer">
                      <span>{cat}</span>
                      <ChevronRight className="w-3.5 h-3.5 text-slate-400" />
                    </span>
                  </li>
                ))}
              </ul>
            </div>
          </aside>
        </div>

        {/* PromptSyra Deep Navy Footer */}
        <div className="bg-[#0B1026] text-slate-300 px-6 py-6 text-xs flex flex-wrap items-center justify-between gap-4">
          <div>
            <span className="font-display font-extrabold text-white text-sm">
              PromptSyra.com
            </span>
            <p className="text-[11px] text-slate-400 mt-0.5">
              Curated AI photo prompts, lighting setups, and creative guides.
            </p>
          </div>
          <span className="text-[11px] text-slate-400">
            © {new Date().getFullYear()} PromptSyra.com. All rights reserved.
          </span>
        </div>
      </div>
    </div>
  );
};
