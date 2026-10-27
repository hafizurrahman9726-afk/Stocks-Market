import React, { useState } from 'react';
import { ArrowRight, CheckCircle2, AlertCircle, Send, Mail } from 'lucide-react';

type PreviewPageId = 'about' | 'contact' | 'privacy' | 'disclaimer' | 'terms' | 'author';

interface AuthorSpec {
  id: 'syra' | 'arjun' | 'neha';
  name: string;
  slug: string;
  role: string;
  initials: string;
  photoUrl: string;
  bio: string;
}

const DESIGNATED_AUTHORS: AuthorSpec[] = [
  {
    id: 'syra',
    name: 'Syra Ansari',
    slug: 'syra-ansari',
    role: 'Author and Editorial Contributor',
    initials: 'SA',
    photoUrl: 'https://promptsyra.com/wp-content/uploads/2026/10/Syra-Ansari.webp',
    bio: 'Syra Ansari contributes AI photo prompt collections, portrait composition examples, and editorial customization guides published on PromptSyra.com, focusing on clear structure and practical adaptability across modern image tools.',
  },
  {
    id: 'arjun',
    name: 'Arjun Patel',
    slug: 'arjun-patel',
    role: 'AI Photo Prompt Contributor',
    initials: 'AP',
    photoUrl: 'https://promptsyra.com/wp-content/uploads/2026/10/Arjun-Patel.webp',
    bio: 'Arjun Patel prepares structured AI photo prompt ideas, lighting and framing variations, and visual style notes for PromptSyra.com readers looking to customize portrait and creative scene concepts.',
  },
  {
    id: 'neha',
    name: 'Neha Joshi',
    slug: 'neha-joshi',
    role: 'Creative Content Contributor',
    initials: 'NJ',
    photoUrl: 'https://promptsyra.com/wp-content/uploads/2026/10/Neha-Joshi.webp',
    bio: 'Neha Joshi contributes trending visual prompt topics, social media image concepts, and step-by-step adaptation tips on PromptSyra.com to help creators tailor prompt wording to their own projects.',
  },
];

function ResilientAuthorPortrait({
  author,
  sizeClass = 'w-[76px] h-[76px]',
}: {
  author: AuthorSpec;
  sizeClass?: string;
}) {
  const [imgFailed, setImgFailed] = useState(false);

  return (
    <div
      className={`${sizeClass} rounded-full overflow-hidden shrink-0 border-2 border-violet-100 bg-violet-50 flex items-center justify-center`}
    >
      {!imgFailed ? (
        <img
          src={author.photoUrl}
          alt={`Portrait of ${author.name}, ${author.role} at PromptSyra.com`}
          referrerPolicy="no-referrer"
          loading="lazy"
          onError={() => setImgFailed(true)}
          className="w-full h-full object-cover object-top"
        />
      ) : (
        <span className="font-display font-bold text-base text-violet-700" aria-hidden="true">
          {author.initials}
        </span>
      )}
    </div>
  );
}

export function EssentialPagesPreview() {
  const [selectedPage, setSelectedPage] = useState<PreviewPageId>('about');
  const [selectedAuthorId, setSelectedAuthorId] = useState<'syra' | 'arjun' | 'neha'>('syra');

  const [contactFields, setContactFields] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
  });
  const [contactState, setContactState] = useState<{
    status: 'idle' | 'error' | 'success';
    message: string;
  }>({ status: 'idle', message: '' });

  const openAuthorArchive = (authorId: 'syra' | 'arjun' | 'neha') => {
    setSelectedAuthorId(authorId);
    setSelectedPage('author');
  };

  const handleContactSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const trimmedName = contactFields.name.trim();
    const trimmedEmail = contactFields.email.trim();
    const trimmedSubject = contactFields.subject.trim();
    const trimmedMessage = contactFields.message.trim();

    if (trimmedName.length < 2) {
      setContactState({
        status: 'error',
        message: 'Please enter your name so we know how to address our reply.',
      });
      return;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(trimmedEmail)) {
      setContactState({
        status: 'error',
        message: 'Please enter a valid email address so we can respond to your inquiry.',
      });
      return;
    }
    if (trimmedSubject.length < 3) {
      setContactState({
        status: 'error',
        message: 'Please enter a brief subject describing your question or feedback.',
      });
      return;
    }
    if (trimmedMessage.length < 10) {
      setContactState({
        status: 'error',
        message: 'Please include a clear message (at least 10 characters) with the details of your inquiry.',
      });
      return;
    }

    setContactState({
      status: 'success',
      message:
        'Workbench Preview Validated: In WordPress, admin-post.php verifies your nonce, honeypot, and rate-limit token, then calls wp_mail() to deliver this message to contact@promptsyra.com.',
    });
  };

  const activeAuthor =
    DESIGNATED_AUTHORS.find((a) => a.id === selectedAuthorId) ?? DESIGNATED_AUTHORS[0];

  const pageTabs: { id: PreviewPageId; label: string; slug: string }[] = [
    { id: 'about', label: 'About Us', slug: '/about-us/' },
    { id: 'contact', label: 'Contact Us', slug: '/contact-us/' },
    { id: 'privacy', label: 'Privacy Policy', slug: '/privacy-policy/' },
    { id: 'disclaimer', label: 'Disclaimer', slug: '/disclaimer/' },
    { id: 'terms', label: 'Terms & Conditions', slug: '/terms-and-conditions/' },
    { id: 'author', label: 'Author Archives', slug: `/author/${activeAuthor.slug}/` },
  ];

  const currentSlug = pageTabs.find((t) => t.id === selectedPage)?.slug ?? '/about-us/';

  return (
    <div className="space-y-5">
      {/* Page Selector Toolbar */}
      <div className="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4">
        <div className="flex flex-wrap items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
          {pageTabs.map((tab) => (
            <button
              key={tab.id}
              type="button"
              onClick={() => setSelectedPage(tab.id)}
              className={`px-3 py-1.5 rounded-md text-xs font-semibold transition-colors whitespace-nowrap shrink-0 ${
                selectedPage === tab.id
                  ? 'bg-white text-slate-900 shadow-xs'
                  : 'text-slate-600 hover:text-slate-900'
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        <div className="text-xs text-slate-500 font-mono">
          https://promptsyra.com{currentSlug}
        </div>
      </div>

      {/* Simulated WordPress Page Frame */}
      <div className="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
        {/* Site Header Chrome */}
        <div className="border-b border-slate-200 px-6 py-4 flex items-center justify-between gap-6 bg-white">
          <div className="flex items-center gap-2.5 shrink-0">
            <span className="w-8 h-8 rounded-lg bg-violet-600 text-white font-display font-extrabold text-sm flex items-center justify-center">
              P
            </span>
            <span className="font-display font-extrabold text-lg tracking-tight text-slate-900">
              Prompt<span className="text-violet-600">Syra</span>
              <span className="text-slate-400 font-semibold text-sm">.com</span>
            </span>
          </div>

          <div className="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            <button
              type="button"
              onClick={() => setSelectedPage('about')}
              className={`transition-colors whitespace-nowrap ${
                selectedPage === 'about' ? 'text-violet-700 font-semibold' : 'hover:text-slate-900'
              }`}
            >
              About Us
            </button>
            <button
              type="button"
              onClick={() => setSelectedPage('contact')}
              className={`transition-colors whitespace-nowrap ${
                selectedPage === 'contact' ? 'text-violet-700 font-semibold' : 'hover:text-slate-900'
              }`}
            >
              Contact Us
            </button>
            <button
              type="button"
              onClick={() => setSelectedPage('privacy')}
              className={`transition-colors whitespace-nowrap ${
                selectedPage === 'privacy' ? 'text-violet-700 font-semibold' : 'hover:text-slate-900'
              }`}
            >
              Privacy Policy
            </button>
            <button
              type="button"
              onClick={() => setSelectedPage('disclaimer')}
              className={`transition-colors whitespace-nowrap ${
                selectedPage === 'disclaimer' ? 'text-violet-700 font-semibold' : 'hover:text-slate-900'
              }`}
            >
              Disclaimer
            </button>
            <button
              type="button"
              onClick={() => setSelectedPage('terms')}
              className={`transition-colors whitespace-nowrap ${
                selectedPage === 'terms' ? 'text-violet-700 font-semibold' : 'hover:text-slate-900'
              }`}
            >
              Terms &amp; Conditions
            </button>
          </div>
        </div>

        {/* 1. ABOUT US PAGE PREVIEW */}
        {selectedPage === 'about' && (
          <div>
            <header className="border-b border-slate-200 px-6 py-11 bg-white">
              <div className="max-w-[1040px] mx-auto">
                <p className="text-xs font-semibold text-violet-700 mb-2">About PromptSyra.com</p>
                <h1 className="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                  About PromptSyra
                </h1>
                <p className="mt-3 text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                  Practical AI photo prompts, clear customization notes, and the editorial contributors behind PromptSyra.com.
                </p>
              </div>
            </header>

            <div className="max-w-[1040px] mx-auto px-6 py-11 space-y-10 text-[16px] leading-[1.78] text-slate-700">
              <div className="max-w-3xl space-y-4">
                <p className="text-lg text-slate-800 font-medium leading-relaxed">
                  PromptSyra.com is an independent editorial library dedicated to practical AI photo prompts, trending visual concepts, social media image ideas, and straightforward customization notes for everyday creators.
                </p>
                <p>
                  Generating believable portraits or stylized scenes with modern AI image tools often involves trial and error when camera angles, lighting descriptions, or background cues are left vague. PromptSyra.com organizes copy-ready prompt examples alongside clear guidance on how to adapt each detail to your own creative project.
                </p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-3">
                  <h2 className="font-display font-bold text-xl text-slate-900">
                    What PromptSyra Publishes
                  </h2>
                  <p className="text-sm text-slate-600">
                    Our catalog focuses on structured text prompts and practical reference guides across key creative areas:
                  </p>
                  <ul className="list-disc pl-5 space-y-2 text-sm text-slate-700">
                    <li>
                      <strong className="text-slate-900">AI Photo Prompts:</strong> Portrait, studio lighting, couple composition, candid street style, and editorial photography prompt examples.
                    </li>
                    <li>
                      <strong className="text-slate-900">Trending Prompts:</strong> Seasonal themes, festive attire concepts, poster-style compositions, and popular visual aesthetics.
                    </li>
                    <li>
                      <strong className="text-slate-900">Social Media Prompts:</strong> Aspect-ratio-aware image prompt ideas suited for profile visuals, story frames, thumbnails, and creator feeds.
                    </li>
                    <li>
                      <strong className="text-slate-900">Customization Guides:</strong> Plain-language notes explaining how changing wardrobe, lighting direction, color palette, lens feel, or background setting alters a prompt.
                    </li>
                  </ul>
                </div>

                <div className="bg-slate-50 border border-slate-200 rounded-2xl p-6 space-y-3">
                  <h2 className="font-display font-bold text-xl text-slate-900">
                    Who PromptSyra Is Built For
                  </h2>
                  <p className="text-sm text-slate-600">
                    PromptSyra.com is written for everyday readers as well as regular digital creators. You do not need technical prompt-engineering jargon to use the library.
                  </p>
                  <ul className="list-disc pl-5 space-y-2 text-sm text-slate-700">
                    <li>
                      <strong className="text-slate-900">Everyday Users &amp; Hobbyists</strong> exploring creative portrait or themed photo ideas for personal projects.
                    </li>
                    <li>
                      <strong className="text-slate-900">Content Creators &amp; Social Media Publishers</strong> looking for consistent visual themes, story backgrounds, and thumbnail concepts.
                    </li>
                    <li>
                      <strong className="text-slate-900">Designers, Bloggers &amp; Marketers</strong> drafting moodboards, concept illustrations, and editorial visual references.
                    </li>
                  </ul>
                </div>
              </div>

              <div className="pt-6 border-t border-slate-100 max-w-3xl space-y-4">
                <h2 className="font-display font-bold text-2xl text-slate-900">
                  How Readers Can Use Prompts Effectively
                </h2>
                <p>
                  Every prompt published on PromptSyra.com is meant to work as an adaptable starting point rather than a rigid script. When copying a prompt from an article, you will usually get better alignment with your idea by tailoring specific descriptors:
                </p>
                <ul className="list-disc pl-5 space-y-2">
                  <li><strong className="text-slate-900">Subject &amp; Wardrobe Details:</strong> Adjust clothing styles, fabrics, eyewear, expressions, or cultural attire to match your subject.</li>
                  <li><strong className="text-slate-900">Lighting &amp; Time of Day:</strong> Swap soft window daylight, golden-hour rim light, or overcast studio diffusion to shift the mood.</li>
                  <li><strong className="text-slate-900">Framing, Camera Feel &amp; Aspect Ratio:</strong> Choose close-up portrait framing, medium waist-up composition, or wide environmental context.</li>
                  <li><strong className="text-slate-900">Color Palette &amp; Setting:</strong> Replace background locations and dominant color tones so the scene feels cohesive.</li>
                </ul>
              </div>

              <div className="pt-6 border-t border-slate-100 max-w-3xl space-y-4">
                <h2 className="font-display font-bold text-2xl text-slate-900">
                  Our Editorial Approach &amp; How Content Is Prepared
                </h2>
                <p>
                  Prompts and companion notes are structured around clear photographic descriptors—subject, pose, environment, lighting, and composition—with one-click copy blocks so readers can immediately see which words control each part of the scene and edit them cleanly without copying surrounding UI text. We avoid exaggerated benchmarks or promises of guaranteed results.
                </p>
              </div>

              <div className="bg-violet-50/50 border border-violet-200/80 rounded-2xl p-6 sm:p-7 space-y-3">
                <h2 className="font-display font-bold text-xl text-slate-900">
                  Understanding the Limitations of AI Image Tools
                </h2>
                <p className="text-[15px]">
                  AI image generators interpret text probabilistically rather than following deterministic rules. Even when using the exact same prompt text, generated images will vary depending on the AI platform you choose (such as ChatGPT, Google Gemini, Midjourney, Grok, or other tools), the active model version, safety filters, aspect-ratio settings, and whether you attach a reference photo.
                </p>
                <p className="text-[15px]">
                  Details such as hands, small background text, intricate jewelry, and consistent facial likeness across multiple generations frequently require wording adjustments or multiple attempts.
                </p>
              </div>

              {/* Meet Our Writers Section */}
              <section className="pt-8 border-t border-slate-200 space-y-6">
                <div className="max-w-2xl space-y-1.5">
                  <p className="text-xs font-semibold text-violet-700">Editorial Contributors</p>
                  <h2 className="font-display font-bold text-2xl sm:text-3xl text-slate-900">
                    Meet Our Writers
                  </h2>
                  <p className="text-sm sm:text-base text-slate-600">
                    Articles and prompt collections on PromptSyra.com are prepared and maintained by our editorial contributors. Select any author card below to preview their dynamic WordPress Author Archive.
                  </p>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                  {DESIGNATED_AUTHORS.map((author) => (
                    <article
                      key={author.id}
                      className="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-300 transition-colors"
                    >
                      <div>
                        <div className="flex items-center gap-4 mb-4">
                          <ResilientAuthorPortrait author={author} />
                          <div className="min-w-0">
                            <h3 className="font-display font-bold text-lg text-slate-900 leading-snug">
                              <button
                                type="button"
                                onClick={() => openAuthorArchive(author.id)}
                                className="hover:text-violet-700 transition-colors text-left"
                              >
                                {author.name}
                              </button>
                            </h3>
                            <p className="text-xs font-medium text-slate-500 mt-0.5">
                              {author.role}
                            </p>
                          </div>
                        </div>

                        <p className="text-sm text-slate-600 leading-relaxed mb-5">
                          {author.bio}
                        </p>
                      </div>

                      <div className="pt-4 border-t border-slate-100">
                        <button
                          type="button"
                          onClick={() => openAuthorArchive(author.id)}
                          className="inline-flex items-center gap-1.5 text-sm font-semibold text-violet-700 hover:text-violet-900 transition-colors"
                        >
                          <span>View {author.name}’s Author Archive</span>
                          <ArrowRight className="w-4 h-4" />
                        </button>
                      </div>
                    </article>
                  ))}
                </div>
              </section>

              <div className="pt-6 border-t border-slate-100 max-w-3xl space-y-4">
                <h2 className="font-display font-bold text-2xl text-slate-900">
                  Accuracy, Updates &amp; Contact
                </h2>
                <p>
                  If you spot an outdated note, broken element, or prompt formatting issue on any PromptSyra.com page, please reach out through our{' '}
                  <button
                    type="button"
                    onClick={() => setSelectedPage('contact')}
                    className="text-violet-700 underline underline-offset-2 font-medium"
                  >
                    Contact Us page
                  </button>{' '}
                  or email{' '}
                  <a
                    href="mailto:contact@promptsyra.com"
                    className="text-violet-700 underline underline-offset-2 font-semibold"
                  >
                    contact@promptsyra.com
                  </a>
                  .
                </p>
              </div>
            </div>
          </div>
        )}

        {/* 2. CONTACT US PAGE PREVIEW */}
        {selectedPage === 'contact' && (
          <div>
            <header className="border-b border-slate-200 px-6 py-11 bg-white">
              <div className="max-w-[1040px] mx-auto">
                <p className="text-xs font-semibold text-violet-700 mb-2">
                  Reader Support &amp; Editorial Inbox
                </p>
                <h1 className="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                  Contact PromptSyra
                </h1>
                <p className="mt-3 text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed">
                  Questions, prompt suggestions, content corrections, or partnership inquiries — get in touch with PromptSyra.com.
                </p>
              </div>
            </header>

            <div className="max-w-[1040px] mx-auto px-6 py-11 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
              <div className="lg:col-span-5 space-y-6 text-[15px] leading-relaxed text-slate-700">
                <p className="text-base text-slate-800 font-medium">
                  Have a question about an article, a suggestion for a new prompt category, or a correction to report? Reach out to the PromptSyra.com editorial inbox using the form on this page or by direct email.
                </p>

                <div className="bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-1.5">
                  <h2 className="font-display font-bold text-base text-slate-900">
                    Official Email Address
                  </h2>
                  <p className="text-sm text-slate-600">For direct email inquiries, write to us at:</p>
                  <a
                    href="mailto:contact@promptsyra.com"
                    className="inline-flex items-center gap-2 font-display font-bold text-lg text-violet-700 hover:underline pt-1"
                  >
                    <Mail className="w-4 h-4" />
                    <span>contact@promptsyra.com</span>
                  </a>
                </div>

                <div className="space-y-3 pt-2 border-t border-slate-100">
                  <h2 className="font-display font-bold text-lg text-slate-900">
                    Topics You Can Contact Us About
                  </h2>
                  <ul className="list-disc pl-5 space-y-2 text-sm text-slate-700">
                    <li><strong className="text-slate-900">General Website Questions:</strong> Navigating PromptSyra.com or how prompt guides are organized.</li>
                    <li><strong className="text-slate-900">Prompt &amp; Category Suggestions:</strong> Portrait themes, creative styles, or social media ideas.</li>
                    <li><strong className="text-slate-900">Corrections &amp; Technical Issues:</strong> Broken links, copy-button issues, or article updates.</li>
                    <li><strong className="text-slate-900">Copyright &amp; Content Concerns:</strong> Attribution, intellectual property, or removal inquiries.</li>
                    <li><strong className="text-slate-900">Advertising &amp; Collaborations:</strong> Relevant sponsorship or partnership questions.</li>
                  </ul>
                </div>
              </div>

              <div className="lg:col-span-7 bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 shadow-xs">
                <div className="mb-6">
                  <h2 className="font-display font-bold text-xl text-slate-900">
                    Send Us a Message
                  </h2>
                  <p className="text-sm text-slate-500 mt-1">
                    Complete all required fields marked with an asterisk (*). Messages are delivered directly to contact@promptsyra.com via WordPress wp_mail().
                  </p>
                </div>

                {contactState.status === 'success' && (
                  <div
                    role="status"
                    className="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-sm flex items-start gap-3"
                  >
                    <CheckCircle2 className="w-5 h-5 text-emerald-700 shrink-0 mt-0.5" />
                    <div>
                      <strong className="block font-semibold">Message Validated</strong>
                      <p className="mt-0.5">{contactState.message}</p>
                    </div>
                  </div>
                )}

                {contactState.status === 'error' && (
                  <div
                    role="alert"
                    className="mb-5 p-4 rounded-xl bg-red-50 border border-red-200 text-red-900 text-sm flex items-start gap-3"
                  >
                    <AlertCircle className="w-5 h-5 text-red-700 shrink-0 mt-0.5" />
                    <div>
                      <strong className="block font-semibold">Unable to Send Message</strong>
                      <p className="mt-0.5">{contactState.message}</p>
                    </div>
                  </div>
                )}

                <form onSubmit={handleContactSubmit} className="space-y-4" noValidate>
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label htmlFor="preview-cf-name" className="block text-sm font-semibold text-slate-900 mb-1.5">
                        Name <span className="text-violet-600">*</span>
                      </label>
                      <input
                        id="preview-cf-name"
                        type="text"
                        value={contactFields.name}
                        onChange={(e) => setContactFields((p) => ({ ...p, name: e.target.value }))}
                        placeholder="Your full name"
                        className="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-violet-600 focus:outline-none focus:ring-3 focus:ring-violet-600/15"
                      />
                    </div>
                    <div>
                      <label htmlFor="preview-cf-email" className="block text-sm font-semibold text-slate-900 mb-1.5">
                        Email Address <span className="text-violet-600">*</span>
                      </label>
                      <input
                        id="preview-cf-email"
                        type="email"
                        value={contactFields.email}
                        onChange={(e) => setContactFields((p) => ({ ...p, email: e.target.value }))}
                        placeholder="you@example.com"
                        className="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-violet-600 focus:outline-none focus:ring-3 focus:ring-violet-600/15"
                      />
                    </div>
                  </div>

                  <div>
                    <label htmlFor="preview-cf-subject" className="block text-sm font-semibold text-slate-900 mb-1.5">
                      Subject <span className="text-violet-600">*</span>
                    </label>
                    <input
                      id="preview-cf-subject"
                      type="text"
                      value={contactFields.subject}
                      onChange={(e) => setContactFields((p) => ({ ...p, subject: e.target.value }))}
                      placeholder="e.g., Prompt suggestion, content correction, or collaboration inquiry"
                      className="w-full min-h-[48px] px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-violet-600 focus:outline-none focus:ring-3 focus:ring-violet-600/15"
                    />
                  </div>

                  <div>
                    <label htmlFor="preview-cf-message" className="block text-sm font-semibold text-slate-900 mb-1.5">
                      Message <span className="text-violet-600">*</span>
                    </label>
                    <textarea
                      id="preview-cf-message"
                      rows={5}
                      value={contactFields.message}
                      onChange={(e) => setContactFields((p) => ({ ...p, message: e.target.value }))}
                      placeholder="Share the page URL, prompt details, or question so we can review it accurately..."
                      className="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-violet-600 focus:outline-none focus:ring-3 focus:ring-violet-600/15"
                    />
                  </div>

                  <p className="text-xs text-slate-500 leading-relaxed">
                    By submitting this form, you agree that PromptSyra.com may use your message and contact details to review and respond to your inquiry, as described in our{' '}
                    <button
                      type="button"
                      onClick={() => setSelectedPage('privacy')}
                      className="text-violet-700 underline underline-offset-2 font-medium"
                    >
                      Privacy Policy
                    </button>
                    .
                  </p>

                  <button
                    type="submit"
                    className="inline-flex items-center justify-center gap-2 min-h-[48px] px-6 py-3 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-display font-bold text-sm transition-colors"
                  >
                    <span>Send Message</span>
                    <Send className="w-4 h-4" />
                  </button>
                </form>
              </div>
            </div>
          </div>
        )}

        {/* 3. PRIVACY POLICY PREVIEW (Unboxed 760px Editorial Legal Prose) */}
        {selectedPage === 'privacy' && (
          <div>
            <header className="border-b border-slate-200 px-6 py-11 bg-white">
              <div className="max-w-[740px] mx-auto">
                <p className="text-xs font-semibold text-violet-700 mb-2">
                  Website Policy &amp; Legal Information
                </p>
                <h1 className="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                  Privacy Policy
                </h1>
                <p className="mt-3 text-base text-slate-600 leading-relaxed">
                  How PromptSyra.com handles visitor messages, comments, cookies, and third-party resources transparently.
                </p>
              </div>
            </header>

            <article className="max-w-[740px] mx-auto px-6 py-11 space-y-6 text-[16px] leading-[1.8] text-slate-700">
              <p className="text-xs text-slate-500 pb-4 border-b border-slate-100">
                <strong className="text-slate-700">Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp;{' '}
                <strong className="text-slate-700">Last Updated:</strong> October 2026
              </p>

              <p>
                This Privacy Policy explains what information PromptSyra.com collects when you visit <strong>https://promptsyra.com</strong>, submit a message through our contact page, or leave a public comment on an article, how that information is used, and how you can contact us with privacy requests.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                1. Information You Voluntarily Provide
              </h2>
              <ul className="list-disc pl-5 space-y-2.5">
                <li><strong className="text-slate-900">Contact Form &amp; Direct Email Inquiries:</strong> Name, email address, subject line, and message sent to <a href="mailto:contact@promptsyra.com" className="text-violet-700 underline">contact@promptsyra.com</a>, plus a temporary 15-minute cryptographic IP hash solely for anti-spam rate limiting.</li>
                <li><strong className="text-slate-900">Public Article Comments:</strong> Name, email address (never displayed publicly), optional website URL, comment text, and standard WordPress anti-spam metadata.</li>
              </ul>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                2. Technical &amp; Server Log Data Collected Automatically
              </h2>
              <p>
                Standard web hosting request logs and <strong>Cloudflare</strong> security/performance delivery (including Cloudflare’s anonymous browser performance beacon) process basic technical request metadata to deliver pages and mitigate automated attacks.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                3. Cookies, Third-Party Fonts, Avatars &amp; Advertising Status
              </h2>
              <ul className="list-disc pl-5 space-y-2.5">
                <li><strong className="text-slate-900">WordPress Functional Cookies:</strong> Optional commenter convenience cookies (<code>comment_author_*</code>) when opted in, and backend login cookies for site editors.</li>
                <li><strong className="text-slate-900">Google Fonts &amp; Gravatar:</strong> Typography stylesheets from <code>fonts.googleapis.com</code> and optional comment avatar hash checks via <code>secure.gravatar.com</code>.</li>
                <li><strong className="text-slate-900">Analytics &amp; Advertising Status:</strong> PromptSyra.com does not currently run third-party behavioral advertising networks or cross-site profiling scripts. If display advertising (such as Google AdSense) or additional analytics tools are enabled in the future, this Privacy Policy will be updated accordingly.</li>
              </ul>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                4. External AI Image Platforms &amp; Privacy Requests
              </h2>
              <p>
                PromptSyra.com is independent of external AI image generators (ChatGPT, Google Gemini, Midjourney, Grok, etc.) and never receives photos or prompts you submit on those platforms. To request comment removal or inquiry deletion, email <a href="mailto:contact@promptsyra.com" className="text-violet-700 font-semibold underline">contact@promptsyra.com</a>.
              </p>
            </article>
          </div>
        )}

        {/* 4. DISCLAIMER PREVIEW (Unboxed 760px Editorial Legal Prose) */}
        {selectedPage === 'disclaimer' && (
          <div>
            <header className="border-b border-slate-200 px-6 py-11 bg-white">
              <div className="max-w-[740px] mx-auto">
                <p className="text-xs font-semibold text-violet-700 mb-2">
                  Website Policy &amp; Legal Information
                </p>
                <h1 className="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                  Disclaimer
                </h1>
                <p className="mt-3 text-base text-slate-600 leading-relaxed">
                  Important context on AI image output variability, customizable prompt examples, and platform independence.
                </p>
              </div>
            </header>

            <article className="max-w-[740px] mx-auto px-6 py-11 space-y-6 text-[16px] leading-[1.8] text-slate-700">
              <p className="text-xs text-slate-500 pb-4 border-b border-slate-100">
                <strong className="text-slate-700">Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp;{' '}
                <strong className="text-slate-700">Last Updated:</strong> October 2026
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900">
                1. Informational &amp; Creative Reference Purpose
              </h2>
              <p>
                All AI photo prompts, composition notes, and customization guides on PromptSyra.com are provided strictly for general informational, educational, and creative inspiration purposes.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                2. AI Image Output Variability &amp; No Guaranteed Results
              </h2>
              <p>
                Generative AI image tools produce different results depending on the platform, model version, safety filters, uploaded reference photo, aspect ratio, and random seed. PromptSyra.com cannot guarantee that copying a prompt will recreate a sample image, preserve exact facial likeness, or achieve a specific outcome.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                3. Independence from Third-Party AI Platforms
              </h2>
              <p>
                References to ChatGPT, OpenAI, Google Gemini, Midjourney, Grok, or social media platforms are purely descriptive. PromptSyra.com is an independent publication and is not affiliated with, endorsed by, or sponsored by any third-party AI provider.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                4. Monetization Transparency &amp; Reporting Errors
              </h2>
              <p>
                If sponsored placements, affiliate links, or display advertisements are included on any page now or in the future, they will be disclosed clearly. Report any errors or concerns to <a href="mailto:contact@promptsyra.com" className="text-violet-700 font-semibold underline">contact@promptsyra.com</a>.
              </p>
            </article>
          </div>
        )}

        {/* 5. TERMS & CONDITIONS PREVIEW (Unboxed 760px Editorial Legal Prose) */}
        {selectedPage === 'terms' && (
          <div>
            <header className="border-b border-slate-200 px-6 py-11 bg-white">
              <div className="max-w-[740px] mx-auto">
                <p className="text-xs font-semibold text-violet-700 mb-2">
                  Website Policy &amp; Legal Information
                </p>
                <h1 className="font-display font-extrabold text-3xl sm:text-4xl text-slate-900 tracking-tight">
                  Terms &amp; Conditions
                </h1>
                <p className="mt-3 text-base text-slate-600 leading-relaxed">
                  Practical guidelines for browsing PromptSyra.com, copying and adapting prompts, and respecting site content.
                </p>
              </div>
            </header>

            <article className="max-w-[740px] mx-auto px-6 py-11 space-y-6 text-[16px] leading-[1.8] text-slate-700">
              <p className="text-xs text-slate-500 pb-4 border-b border-slate-100">
                <strong className="text-slate-700">Website:</strong> PromptSyra.com (https://promptsyra.com) &nbsp;·&nbsp;{' '}
                <strong className="text-slate-700">Last Updated:</strong> October 2026
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900">
                1. Permitted Use of Prompts &amp; Guides
              </h2>
              <p>
                Visitors are welcome to copy individual text prompts from PromptSyra.com, customize their wording, and paste them into compatible third-party AI image tools (subject to the terms of the AI platform used).
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                2. Intellectual Property &amp; Bulk Scraping Restrictions
              </h2>
              <p>
                Original articles, editorial guides, compilation structure, layouts, and PromptSyra.com branding remain protected intellectual property. Systematic scraping or verbatim bulk republication of full PromptSyra articles on competing websites or paid prompt packs without written permission is prohibited.
              </p>

              <h2 className="font-display font-bold text-xl text-slate-900 pt-5 border-t border-slate-100">
                3. User Conduct, Responsible AI Use &amp; Inquiries
              </h2>
              <p>
                Visitors agree not to submit spam, malicious links, or infringing content in comments or forms, and remain responsible for complying with applicable laws and third-party rights when generating AI visuals. Direct copyright or terms inquiries to <a href="mailto:contact@promptsyra.com" className="text-violet-700 font-semibold underline">contact@promptsyra.com</a>.
              </p>
            </article>
          </div>
        )}

        {/* 6. AUTHOR ARCHIVE PREVIEW */}
        {selectedPage === 'author' && (
          <div className="max-w-[1040px] mx-auto px-6 py-10 space-y-8">
            <div className="flex flex-wrap items-center justify-between gap-4">
              <div className="text-xs text-slate-500 flex items-center gap-2">
                <span>PromptSyra.com</span>
                <span>/</span>
                <button
                  type="button"
                  onClick={() => setSelectedPage('about')}
                  className="hover:text-violet-700 underline"
                >
                  About Us
                </button>
                <span>/</span>
                <span className="text-slate-900 font-medium">{activeAuthor.name}</span>
              </div>

              <div className="flex items-center gap-1.5 bg-slate-100 p-1 rounded-lg">
                {DESIGNATED_AUTHORS.map((a) => (
                  <button
                    key={a.id}
                    type="button"
                    onClick={() => setSelectedAuthorId(a.id)}
                    className={`px-3 py-1 rounded-md text-xs font-semibold transition-colors ${
                      selectedAuthorId === a.id
                        ? 'bg-white text-slate-900 shadow-xs'
                        : 'text-slate-600 hover:text-slate-900'
                    }`}
                  >
                    {a.name}
                  </button>
                ))}
              </div>
            </div>

            <div className="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start gap-6">
              <ResilientAuthorPortrait author={activeAuthor} sizeClass="w-24 h-24" />
              <div className="space-y-2">
                <h1 className="font-display font-extrabold text-2xl sm:text-3xl text-slate-900">
                  {activeAuthor.name}
                </h1>
                <p className="text-sm font-semibold text-violet-700">
                  {activeAuthor.role} · PromptSyra.com
                </p>
                <p className="text-[15px] text-slate-600 leading-relaxed max-w-2xl">
                  {activeAuthor.bio}
                </p>
                <p className="text-xs text-slate-500 pt-1">
                  WordPress Author Slug: <code>/author/{activeAuthor.slug}/</code> · Portrait Source: Verified Media Library WebP
                </p>
              </div>
            </div>

            <div className="bg-slate-50 border border-slate-200 rounded-xl p-6 text-sm text-slate-600">
              <p className="font-medium text-slate-800">
                Non-Destructive Post Attribution Policy Active
              </p>
              <p className="mt-1">
                Existing published posts on PromptSyra.com (such as <code>/hello-world/</code>) are never automatically reassigned. When you assign new or reviewed articles to <strong>{activeAuthor.name}</strong> in the WordPress Post Editor, they appear automatically in this dynamic author archive grid.
              </p>
            </div>
          </div>
        )}

        {/* Simulated Site Footer */}
        <footer className="bg-slate-950 text-slate-300 px-6 py-8 border-t border-slate-900">
          <div className="max-w-[1040px] mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs">
            <div className="space-y-1">
              <p className="font-display font-bold text-white text-sm">PromptSyra.com</p>
              <p className="text-slate-400">
                Official Contact:{' '}
                <a href="mailto:contact@promptsyra.com" className="text-violet-300 hover:underline">
                  contact@promptsyra.com
                </a>
              </p>
            </div>
            <div className="flex flex-wrap items-center gap-4 text-slate-400">
              <button type="button" onClick={() => setSelectedPage('about')} className="hover:text-white">
                About Us
              </button>
              <button type="button" onClick={() => setSelectedPage('contact')} className="hover:text-white">
                Contact Us
              </button>
              <button type="button" onClick={() => setSelectedPage('privacy')} className="hover:text-white">
                Privacy Policy
              </button>
              <button type="button" onClick={() => setSelectedPage('disclaimer')} className="hover:text-white">
                Disclaimer
              </button>
              <button type="button" onClick={() => setSelectedPage('terms')} className="hover:text-white">
                Terms &amp; Conditions
              </button>
            </div>
          </div>
        </footer>
      </div>
    </div>
  );
}
