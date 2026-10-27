import JSZip from 'jszip';

import styleCssRaw from '../../wordpress-theme/promptsyra/style.css?raw';
import functionsPhpRaw from '../../wordpress-theme/promptsyra/functions.php?raw';
import customizerPhpRaw from '../../wordpress-theme/promptsyra/inc/customizer.php?raw';
import adminSetupPhpRaw from '../../wordpress-theme/promptsyra/inc/admin-setup.php?raw';
import headerPhpRaw from '../../wordpress-theme/promptsyra/template-parts/header-promptsyra.php?raw';
import footerPhpRaw from '../../wordpress-theme/promptsyra/template-parts/footer-promptsyra.php?raw';
import frontPagePhpRaw from '../../wordpress-theme/promptsyra/front-page.php?raw';
import indexPhpRaw from '../../wordpress-theme/promptsyra/index.php?raw';
import searchPhpRaw from '../../wordpress-theme/promptsyra/search.php?raw';
import pagePhpRaw from '../../wordpress-theme/promptsyra/page.php?raw';
import notFoundPhpRaw from '../../wordpress-theme/promptsyra/404.php?raw';
import singleStandalonePhpRaw from '../../wordpress-theme/promptsyra/single-standalone.php?raw';
import commentsPhpRaw from '../../wordpress-theme/promptsyra/comments.php?raw';
import singleBackupPhpRaw from '../../wordpress-theme/promptsyra/legacy-backup/single-v2.0.0.backup.php?raw';
import singleV21BackupPhpRaw from '../../wordpress-theme/promptsyra/legacy-backup/single-v2.1.0.backup.php?raw';
import pagesV22BackupPhpRaw from '../../wordpress-theme/promptsyra/legacy-backup/pages-authors-v2.2.0.backup.php?raw';
import frontPageV23BackupPhpRaw from '../../wordpress-theme/promptsyra/legacy-backup/front-page-v2.3.0.backup.php?raw';
import singlePostFeaturesPhpRaw from '../../wordpress-theme/promptsyra/inc/single-post-features.php?raw';
import contactFormPhpRaw from '../../wordpress-theme/promptsyra/inc/contact-form.php?raw';
import essentialPagesPhpRaw from '../../wordpress-theme/promptsyra/inc/essential-pages-and-authors.php?raw';
import categoryPhpRaw from '../../wordpress-theme/promptsyra/category.php?raw';
import authorPhpRaw from '../../wordpress-theme/promptsyra/author.php?raw';
import scopedCssRaw from '../../wordpress-theme/promptsyra/assets/css/promptsyra-home.css?raw';
import scopedJsRaw from '../../wordpress-theme/promptsyra/assets/js/promptsyra-home.js?raw';
import singleCssRaw from '../../wordpress-theme/promptsyra/assets/css/promptsyra-single.css?raw';
import singleJsRaw from '../../wordpress-theme/promptsyra/assets/js/promptsyra-single.js?raw';
import pagesCssRaw from '../../wordpress-theme/promptsyra/assets/css/promptsyra-pages.css?raw';
import readmeMdRaw from '../../wordpress-theme/promptsyra/README-DEPLOY.md?raw';
import pluginBootstrapRaw from '../../wordpress-theme/promptsyra-plugin/promptsyra-rebrand.php?raw';
import screenshotPngUrl from '../../wordpress-theme/promptsyra/screenshot.png';

export interface ThemeSourceFile {
  relativePath: string;
  language: 'php' | 'css' | 'javascript' | 'markdown';
  purpose: string;
  singlePostImpact: string;
  content: string;
}

export const PROMPTSYRA_THEME_FILES: ThemeSourceFile[] = [
  {
    relativePath: 'single.php',
    language: 'php',
    purpose:
      'Streamlined v2.2.0 2-column single-post article template with single-post breadcrumb row and standalone category badge cleanly removed, preserving real post title, content, metadata, comments, and sticky right sidebar.',
    singlePostImpact: 'Primary single-post template — dynamically renders /hello-world/ and all prompt articles.',
    content: singleStandalonePhpRaw,
  },
  {
    relativePath: 'comments.php',
    language: 'php',
    purpose:
      'Native WordPress comment section & "Join the Conversation" form template (wp_list_comments + comment_form) with editable heading/subtext, full-width labeled fields, preserved cookie consent, and violet Post Comment button.',
    singlePostImpact: 'Replaces unstyled default WordPress comment form on single posts while preserving all WP comment functionality.',
    content: commentsPhpRaw,
  },
  {
    relativePath: 'inc/single-post-features.php',
    language: 'php',
    purpose:
      'Provides reading-time helper, [promptsyra_prompt] shortcode, native Gutenberg block (promptsyra/ai-prompt-box), and conditional auto-TOC (3+ headings when no TOC plugin is active).',
    singlePostImpact: 'Enhances single posts with interactive AI Prompt Cards and conditional TOC without altering short posts.',
    content: singlePostFeaturesPhpRaw,
  },
  {
    relativePath: 'assets/css/promptsyra-single.css',
    language: 'css',
    purpose:
      'Scoped single-post stylesheet (.ps-single-body, .ps-single-layout, .ps-ai-prompt-box, .ps-comments-section) for 2-column sticky layout, responsive tables, Copy/View buttons, and Join the Conversation comment form.',
    singlePostImpact: 'Enqueued strictly on is_singular("post"); zero impact on front-page.php or archives.',
    content: singleCssRaw,
  },
  {
    relativePath: 'assets/js/promptsyra-single.js',
    language: 'javascript',
    purpose:
      'Accessible View Prompt expand/collapse toggle, clean prompt-only Copy Prompt clipboard handler, TOC toggle, and progressive accessible validation + loading state on #commentform.',
    singlePostImpact: 'Enqueued strictly on is_singular("post"); zero impact on front-page.php.',
    content: singleJsRaw,
  },
  {
    relativePath: 'style.css',
    language: 'css',
    purpose:
      'Defines PromptSyra v2.4.1 standalone WordPress theme metadata with the streamlined 5-destination header navigation and Important Pages dropdown.',
    singlePostImpact: 'Zero global CSS selectors.',
    content: styleCssRaw,
  },
  {
    relativePath: 'functions.php',
    language: 'php',
    purpose:
      'Registers menus, Customizer, approved categories spec (with live ai-social-media-prompts alias resolution), 3 designated authors resolver, SEO plugin checks, and conditional asset enqueuing.',
    singlePostImpact:
      'Enqueues promptsyra-single.css and promptsyra-single.js strictly on is_singular("post").',
    content: functionsPhpRaw,
  },
  {
    relativePath: 'front-page.php',
    language: 'php',
    purpose:
      'PromptSyra.com homepage (v2.4.1): Hero visual composition with 3 real photography showcase images (Portrait Study, Creative Studio Setup, and 9:16 Social Visuals), integrated Feature Benefits Bar, 3 tailored Category Discovery Cards, WP_Query Featured Prompts, Connected 3-Step Workflow, Why Choose PromptSyra? Specimen, and 3 Authors.',
    singlePostImpact: 'Zero impact — WordPress loads front-page.php strictly on the site front page.',
    content: frontPagePhpRaw,
  },
  {
    relativePath: 'index.php',
    language: 'php',
    purpose:
      'Required core WordPress fallback template so Appearance -> Themes -> Upload Theme always validates cleanly.',
    singlePostImpact: 'Zero impact on single.php in Child Theme or Plugin mode.',
    content: indexPhpRaw,
  },
  {
    relativePath: 'search.php',
    language: 'php',
    purpose: 'Renders prompt search results (?s=...) triggered from the Hero and Header search bars.',
    singlePostImpact: 'Zero impact — applies to search queries only.',
    content: searchPhpRaw,
  },
  {
    relativePath: 'page.php',
    language: 'php',
    purpose:
      'v2.3.0 editorial, contact, and unboxed legal page template (/about-us/, /contact-us/, /privacy-policy/, /disclaimer/, /terms-and-conditions/) with smart legacy-stub protection.',
    singlePostImpact: 'Zero impact — applies to WordPress pages only.',
    content: pagePhpRaw,
  },
  {
    relativePath: 'inc/essential-pages-and-authors.php',
    language: 'php',
    purpose:
      'Provides [promptsyra_meet_writers] shortcode, original content specs for all 5 essential pages, 301 legacy-slug bridging (/about/ <-> /about-us/, /contact/ <-> /contact-us/), and non-destructive 1-click WP-Admin author/page sync.',
    singlePostImpact: 'Zero impact on existing published post content or authorship.',
    content: essentialPagesPhpRaw,
  },
  {
    relativePath: 'inc/contact-form.php',
    language: 'php',
    purpose:
      'Provides [promptsyra_contact_form] shortcode and secure admin-post.php handler (nonce, honeypot, signed timestamp, hashed-IP rate limit, honest wp_mail() verification) delivering messages to contact@promptsyra.com.',
    singlePostImpact: 'Zero impact — renders strictly where [promptsyra_contact_form] is placed.',
    content: contactFormPhpRaw,
  },
  {
    relativePath: 'assets/css/promptsyra-pages.css',
    language: 'css',
    purpose:
      'Scoped stylesheet for About Us pillars, 3-column Meet Our Writers cards, Contact Us 2-column form, unboxed 760px legal prose, and Author Archive cards.',
    singlePostImpact: 'Enqueued strictly on is_page(), is_author(), and is_404().',
    content: pagesCssRaw,
  },
  {
    relativePath: 'template-parts/header-promptsyra.php',
    language: 'php',
    purpose:
      'Accessible PromptSyra.com shared header (v2.4.1) featuring Home, AI Photo Prompts, Trending Prompts, Social Media Prompts, and Important Pages dropdown (About Us, Contact Us, Privacy Policy, Disclaimer, Terms & Conditions), Search drawer, and mobile drawer.',
    singlePostImpact: 'Shared across all public pages (front-page.php, single.php, page.php, author.php, category.php, 404.php).',
    content: headerPhpRaw,
  },
  {
    relativePath: 'template-parts/footer-promptsyra.php',
    language: 'php',
    purpose:
      'Responsive deep-navy footer with PromptSyra.com summary, 3 approved categories, verified existing legal page links (no 404s), configurable official socials, and copyright.',
    singlePostImpact: 'Zero impact on single.php unless explicitly included.',
    content: footerPhpRaw,
  },
  {
    relativePath: 'inc/customizer.php',
    language: 'php',
    purpose:
      'Native WordPress Customizer controls (Appearance -> Customize -> PromptSyra Brand & Homepage) with sanitized inputs for copy, CTAs, visibility, socials, FAQs, and author mapping.',
    singlePostImpact: 'Zero impact — manages front-page and brand settings only.',
    content: customizerPhpRaw,
  },
  {
    relativePath: 'inc/admin-setup.php',
    language: 'php',
    purpose:
      'Adds Appearance -> PromptSyra Setup audit screen (nonce + capability protected) to create missing approved categories without duplicates/deletions, audit 3 authors, and edit profile photo URLs.',
    singlePostImpact: 'Zero impact — never alters existing post content or authorship.',
    content: adminSetupPhpRaw,
  },
  {
    relativePath: 'category.php',
    language: 'php',
    purpose:
      'Renders /category/ai-photo-prompts/, /category/trending-prompts/, /category/social-media-prompts/, and legacy archives with breadcrumbs and pagination.',
    singlePostImpact: 'Zero impact — applies to category archives only.',
    content: categoryPhpRaw,
  },
  {
    relativePath: 'author.php',
    language: 'php',
    purpose:
      'Renders real WordPress author archives for Syra Ansari, Arjun Patel, Neha Joshi, and existing post authors without fabricating bios or credentials.',
    singlePostImpact: 'Zero impact — applies to author archives only.',
    content: authorPhpRaw,
  },
  {
    relativePath: 'assets/css/promptsyra-home.css',
    language: 'css',
    purpose:
      'v2.4.0 editorial lavender, crisp white, deep navy, and violet design system strictly namespaced under .ps-* classes, styling the 4 upgraded homepage sections without global CSS bleed.',
    singlePostImpact: 'Zero impact — zero bare tag selectors; cannot bleed into single.php.',
    content: scopedCssRaw,
  },
  {
    relativePath: 'assets/js/promptsyra-home.js',
    language: 'javascript',
    purpose:
      'Accessible keyboard/mouse dropdowns, Escape handling, mobile menu drawer, search drawer, FAQ accordion, and working [data-ps-sample-copy] clipboard handler with fallback & aria-live status.',
    singlePostImpact: 'Zero impact — binds exclusively to [data-ps-*] attributes.',
    content: scopedJsRaw,
  },
  {
    relativePath: 'legacy-backup/front-page-v2.3.0.backup.php',
    language: 'php',
    purpose:
      'Recoverable backup snapshot preserved prior to the v2.4.0 Master Homepage Four-Section UI Upgrade.',
    singlePostImpact: 'Backup file only — never executed by WordPress.',
    content: frontPageV23BackupPhpRaw,
  },
  {
    relativePath: 'legacy-backup/pages-authors-v2.2.0.backup.php',
    language: 'php',
    purpose:
      'Recoverable backup snapshot of v2.2.0 page.php, author.php, and designated author specs preserved prior to the v2.3.0 author & essential pages release.',
    singlePostImpact: 'Backup file only — never executed by WordPress.',
    content: pagesV22BackupPhpRaw,
  },
  {
    relativePath: 'legacy-backup/single-v2.1.0.backup.php',
    language: 'php',
    purpose:
      'Recoverable backup copy of the v2.1.0 single-post template preserved prior to the v2.2.0 breadcrumb/category-badge removal and comment redesign.',
    singlePostImpact: 'Backup file only — never executed by WordPress unless manually restored.',
    content: singleV21BackupPhpRaw,
  },
  {
    relativePath: 'legacy-backup/single-v2.0.0.backup.php',
    language: 'php',
    purpose:
      'Recoverable backup copy of the v2.0.0 single-post template preserved prior to the v2.1.0 single-post article rebuild.',
    singlePostImpact: 'Backup file only — never executed by WordPress unless manually restored.',
    content: singleBackupPhpRaw,
  },
  {
    relativePath: 'README-DEPLOY.md',
    language: 'markdown',
    purpose:
      'Complete backup, v2.3.0 upgrade ("Replace active with uploaded"), 1-click Author & Essential Pages sync, contact@promptsyra.com mail verification, and rollback instructions.',
    singlePostImpact: 'Documentation file only.',
    content: readmeMdRaw,
  },
];

export interface InspectedZipFile {
  path: string;
  cleanPath: string;
  sizeBytes: number;
  brandMatches: number;
  protectionStatus: 'locked-single-post' | 'preserved-parent' | 'inspected-metadata';
  snippet?: string;
}

export interface InspectedThemeReport {
  originalFileName: string;
  sha256Hex: string;
  detectedRootFolder: string;
  detectedThemeName: string;
  detectedVersion: string;
  totalFiles: number;
  hasSinglePhp: boolean;
  singlePhpPath: string | null;
  hasFrontPagePhp: boolean;
  hasFunctionsPhp: boolean;
  brandOccurrencesCount: number;
  files: InspectedZipFile[];
  rawArrayBuffer: ArrayBuffer;
}

export async function computeSha256Hex(buffer: ArrayBuffer): Promise<string> {
  const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
  const hashArray = Array.from(new Uint8Array(hashBuffer));
  return hashArray.map((b) => b.toString(16).padStart(2, '0')).join('');
}

export async function inspectUploadedThemeZip(file: File): Promise<InspectedThemeReport> {
  const arrayBuffer = await file.arrayBuffer();
  const sha256Hex = await computeSha256Hex(arrayBuffer);
  const zip = await JSZip.loadAsync(arrayBuffer);

  const entries = Object.values(zip.files).filter((entry) => !entry.dir && !entry.name.includes('__MACOSX'));

  let detectedRootFolder = 'aipromptzyra';
  if (entries.length > 0 && entries[0].name.includes('/')) {
    const candidateRoot = entries[0].name.split('/')[0];
    const allShareRoot = entries.every((e) => e.name.startsWith(`${candidateRoot}/`));
    if (allShareRoot && candidateRoot.trim().length > 0) {
      detectedRootFolder = candidateRoot;
    }
  }

  let detectedThemeName = 'Unknown Theme';
  let detectedVersion = 'Unknown';
  let hasSinglePhp = false;
  let singlePhpPath: string | null = null;
  let hasFrontPagePhp = false;
  let hasFunctionsPhp = false;
  let brandOccurrencesCount = 0;

  const inspectedFiles: InspectedZipFile[] = [];

  for (const entry of entries) {
    const cleanPath = entry.name.startsWith(`${detectedRootFolder}/`)
      ? entry.name.slice(detectedRootFolder.length + 1)
      : entry.name;

    const lower = cleanPath.toLowerCase();
    const isText = /\.(php|css|js|json|txt|md|html|xml|pot)$/i.test(lower);
    let textContent = '';
    let brandMatches = 0;

    if (isText) {
      textContent = await entry.async('string');
      const matches = textContent.match(/aipromptzyra/gi);
      brandMatches = matches ? matches.length : 0;
      brandOccurrencesCount += brandMatches;
    }

    const byteData = await entry.async('uint8array');

    if (lower === 'style.css' && textContent) {
      const nameMatch = textContent.match(/Theme Name:\s*(.+)/i);
      const verMatch = textContent.match(/Version:\s*(.+)/i);
      if (nameMatch) detectedThemeName = nameMatch[1].trim();
      if (verMatch) detectedVersion = verMatch[1].trim();
    }

    if (lower === 'single.php' || lower.includes('single-post') || lower.includes('content-single')) {
      if (lower === 'single.php') {
        hasSinglePhp = true;
        singlePhpPath = cleanPath;
      }
    }
    if (lower === 'front-page.php' || lower === 'home.php') {
      hasFrontPagePhp = true;
    }
    if (lower === 'functions.php') {
      hasFunctionsPhp = true;
    }

    const isSingleRelated =
      lower === 'single.php' ||
      lower.includes('single') ||
      lower.includes('copy-prompt') ||
      lower.includes('related');

    inspectedFiles.push({
      path: entry.name,
      cleanPath,
      sizeBytes: byteData.byteLength,
      brandMatches,
      protectionStatus: isSingleRelated
        ? 'locked-single-post'
        : lower === 'style.css'
          ? 'inspected-metadata'
          : 'preserved-parent',
      snippet: isText ? textContent.slice(0, 320) : undefined,
    });
  }

  inspectedFiles.sort((a, b) => a.cleanPath.localeCompare(b.cleanPath));

  return {
    originalFileName: file.name,
    sha256Hex,
    detectedRootFolder,
    detectedThemeName,
    detectedVersion,
    totalFiles: inspectedFiles.length,
    hasSinglePhp,
    singlePhpPath,
    hasFrontPagePhp,
    hasFunctionsPhp,
    brandOccurrencesCount,
    files: inspectedFiles,
    rawArrayBuffer: arrayBuffer,
  };
}

/**
 * Generates a ready-to-install WordPress Child Theme ZIP (`promptsyra-child-v2.0.0.zip`)
 * linked to the exact parent theme folder slug (`Template: <parentSlug>`).
 * Keeps the original AIPromptZyra theme and `single.php` 100% untouched.
 */
export async function buildChildThemeZipBlob(parentFolderSlug: string): Promise<Blob> {
  const cleanSlug = parentFolderSlug.trim() || 'aipromptzyra';
  const zip = new JSZip();
  const root = zip.folder('promptsyra-child');
  if (!root) {
    throw new Error('Unable to initialize ZIP directory.');
  }

  for (const file of PROMPTSYRA_THEME_FILES) {
    let content = file.content;
    if (file.relativePath === 'style.css') {
      content = content.replace(/^(Version:\s*.+)$/m, `Template: ${cleanSlug}\n$1`);
    }
    root.file(file.relativePath, content);
  }

  try {
    const res = await fetch(screenshotPngUrl);
    if (res.ok) {
      const screenshotBlob = await res.blob();
      root.file('screenshot.png', screenshotBlob);
    }
  } catch (err) {
    console.warn('Could not load screenshot.png for child theme zip', err);
  }

  return zip.generateAsync({ type: 'blob' });
}

/**
 * Generates a Complete Standalone WordPress Theme ZIP (`promptsyra-v2.0.0.zip`)
 * for `Appearance -> Themes -> Add New Theme -> Upload Theme`.
 * Contains NO `Template:` dependency at all, so WordPress NEVER complains about
 * a missing parent theme ("aipromptzyra").
 */
export async function buildStandaloneThemeZipBlob(): Promise<Blob> {
  const zip = new JSZip();
  const root = zip.folder('promptsyra');
  if (!root) {
    throw new Error('Unable to initialize theme ZIP directory.');
  }

  for (const file of PROMPTSYRA_THEME_FILES) {
    let content = file.content;
    if (file.relativePath === 'style.css') {
      content = content.replace(/^Template:.*\r?\n/gm, '');
    }
    root.file(file.relativePath, content);
  }

  root.file('404.php', notFoundPhpRaw);
  root.file('single.php', singleStandalonePhpRaw);

  try {
    const res = await fetch(screenshotPngUrl);
    if (res.ok) {
      const screenshotBlob = await res.blob();
      root.file('screenshot.png', screenshotBlob);
    }
  } catch (err) {
    console.warn('Could not load screenshot.png for theme zip', err);
  }

  return zip.generateAsync({ type: 'blob' });
}

/**
 * Generates a 1-click WordPress Companion Plugin ZIP (`promptsyra-rebrand-plugin-v2.0.0.zip`).
 * Works alongside AIPromptZyra v1.1.4 regardless of the parent theme folder's exact casing/slug,
 * never modifies files inside wp-content/themes/, and guarantees single.php is 100% untouched.
 */
export async function buildWordPressPluginZipBlob(): Promise<Blob> {
  const zip = new JSZip();
  const root = zip.folder('promptsyra-rebrand');
  if (!root) {
    throw new Error('Unable to initialize plugin ZIP directory.');
  }

  const adaptTemplatePartCalls = (phpSource: string): string =>
    phpSource
      .replace(
        /get_template_part\(\s*'template-parts\/header',\s*'promptsyra'\s*\);/g,
        "promptsyra_load_part( 'template-parts/header', 'promptsyra' );"
      )
      .replace(
        /get_template_part\(\s*'template-parts\/footer',\s*'promptsyra'\s*\);/g,
        "promptsyra_load_part( 'template-parts/footer', 'promptsyra' );"
      );

  root.file('promptsyra-rebrand.php', pluginBootstrapRaw);
  root.file('inc/customizer.php', customizerPhpRaw);
  root.file('inc/admin-setup.php', adminSetupPhpRaw);
  root.file('template-parts/header-promptsyra.php', headerPhpRaw);
  root.file('template-parts/footer-promptsyra.php', footerPhpRaw);
  root.file('front-page.php', adaptTemplatePartCalls(frontPagePhpRaw));
  root.file('category.php', adaptTemplatePartCalls(categoryPhpRaw));
  root.file('author.php', adaptTemplatePartCalls(authorPhpRaw));
  root.file('assets/css/promptsyra-home.css', scopedCssRaw);
  root.file('assets/js/promptsyra-home.js', scopedJsRaw);
  root.file('README-DEPLOY.md', readmeMdRaw);

  return zip.generateAsync({ type: 'blob' });
}

/**
 * Generates a Cloned Standalone Rebranded Theme ZIP (`promptsyra-theme-v2.0.0.zip`)
 * from an uploaded AIPromptZyra ZIP.
 * - Installs into a NEW directory `promptsyra/` so original `aipromptzyra/` is never overwritten.
 * - Copies `single.php` and all single-post templates/scripts byte-for-byte (0 modifications).
 * - Adds the new `front-page.php`, scoped CSS/JS, Customizer, and safe Admin Setup page.
 */
export async function buildMergedClonedThemeZipBlob(report: InspectedThemeReport): Promise<Blob> {
  const sourceZip = await JSZip.loadAsync(report.rawArrayBuffer);
  const outZip = new JSZip();
  const outRoot = outZip.folder('promptsyra');
  if (!outRoot) {
    throw new Error('Unable to initialize output theme directory.');
  }

  const entries = Object.values(sourceZip.files).filter((e) => !e.dir && !e.name.includes('__MACOSX'));

  for (const entry of entries) {
    const cleanPath = entry.name.startsWith(`${report.detectedRootFolder}/`)
      ? entry.name.slice(report.detectedRootFolder.length + 1)
      : entry.name;
    const lower = cleanPath.toLowerCase();

    if (lower === 'style.css') {
      const origCss = await entry.async('string');
      // Update only the top WordPress comment header if present, preserving every single CSS rule below it untouched for single.php!
      const headerUpdated = origCss.replace(/\/\*[\s\S]*?\*\//, () => {
        return [
          '/*',
          'Theme Name: PromptSyra',
          'Theme URI: https://promptsyra.com/',
          'Author: PromptSyra Editorial Team',
          'Description: Rebranded PromptSyra.com WordPress theme (cloned safely from AIPromptZyra ' +
            report.detectedVersion +
            '). Preserves existing single.php layout, tables, and copy-prompt scripts untouched.',
          'Version: 2.0.0',
          'Text Domain: promptsyra',
          '*/',
        ].join('\n');
      });
      outRoot.file(cleanPath, headerUpdated);
    } else if (lower === 'functions.php') {
      const origFunctions = await entry.async('string');
      const cleanOrig = origFunctions.replace(/\?>\s*$/, '');
      const bootstrapAppend = `\n\n// PromptSyra v2.0.0 Modular Rebrand Bootstrap (Single-Post Safe)\nif ( file_exists( get_template_directory() . '/inc/promptsyra-bootstrap.php' ) ) {\n\trequire_once get_template_directory() . '/inc/promptsyra-bootstrap.php';\n}\n`;
      outRoot.file(cleanPath, cleanOrig + bootstrapAppend);
    } else if (lower === 'front-page.php') {
      // Back up legacy front-page.php inside legacy-backup/ before writing the new front-page.php
      const legacyBytes = await entry.async('uint8array');
      outRoot.file('legacy-backup/front-page.original.php', legacyBytes);
    } else {
      // Byte-for-byte preservation (including single.php, tables, copy-prompt JS, header.php, footer.php!)
      const rawBytes = await entry.async('uint8array');
      outRoot.file(cleanPath, rawBytes);
    }
  }

  // Write modular PromptSyra files (adapting PROMPTSYRA_DIR/URI to get_template_directory for standalone clone mode)
  const standaloneBootstrap = functionsPhpRaw
    .replace('get_stylesheet_directory()', 'get_template_directory()')
    .replace('get_stylesheet_directory_uri()', 'get_template_directory_uri()');

  outRoot.file('inc/promptsyra-bootstrap.php', standaloneBootstrap);
  outRoot.file('inc/customizer.php', customizerPhpRaw);
  outRoot.file('inc/admin-setup.php', adminSetupPhpRaw);
  outRoot.file('template-parts/header-promptsyra.php', headerPhpRaw);
  outRoot.file('template-parts/footer-promptsyra.php', footerPhpRaw);
  outRoot.file('front-page.php', frontPagePhpRaw);
  outRoot.file('assets/css/promptsyra-home.css', scopedCssRaw);
  outRoot.file('assets/js/promptsyra-home.js', scopedJsRaw);
  outRoot.file('README-DEPLOY.md', readmeMdRaw);

  return outZip.generateAsync({ type: 'blob' });
}

export function triggerBlobDownload(blob: Blob, filename: string): void {
  const url = URL.createObjectURL(blob);
  const anchor = document.createElement('a');
  anchor.href = url;
  anchor.download = filename;
  document.body.appendChild(anchor);
  anchor.click();
  document.body.removeChild(anchor);
  setTimeout(() => URL.revokeObjectURL(url), 1500);
}
