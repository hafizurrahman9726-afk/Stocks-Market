# PromptSyra WordPress Theme (`v2.4.0`) — Master Homepage Four-Section UI Upgrade

## Versioning & Architecture
- **Previous Releases:** `AIPromptZyra v1.1.4` → `PromptSyra v2.0.0` (Homepage Redesign) → `PromptSyra v2.1.0` (Single-Post Rebuild) → `PromptSyra v2.2.0` (Single-Post Final UI Fix) → `PromptSyra v2.3.0` (3 Dynamic Authors, About Us, Contact Us & Legal Pages)
- **Current Release:** `PromptSyra` — Version `2.4.0` (Master Homepage Four-Section Editorial UI Upgrade + Live WordPress Category Alias Support).
- **Standalone Self-Contained Package:** Zero `Template:` parent-theme dependency in `style.css` — installs and upgrades cleanly via `Appearance → Themes → Add New Theme → Upload Theme`.

## What's New in `v2.4.0`
1. **Section 1 — Hero Supporting CTA & Feature Benefits Bar (`.ps-hero-support-bar`):**
   - Replaces the plain standalone CTA and loose text bullets with an integrated split action & benefits bar anchored directly beneath the Hero search bar, pairing the **Explore Prompts** button with three compact, Customizer-editable benefit items (*Structured, Copy-Ready Text*, *Portrait, Creative & Social Styles*, *Practical Customization Notes*) and crisp SVG check icons.
2. **Section 2 — Primary Category Discovery Cards (`.ps-category-trio`):**
   - Upgraded into three distinct editorial discovery cards (**AI Photo Prompts**, **Trending Prompts**, and **Social Media Prompts**) with custom SVG illustrations, photographic focus kickers, full-card clickable navigation, `:focus-visible` states, conditional real WordPress post counts (never invented numbers), and automatic live slug alias resolution for `/category/ai-social-media-prompts/` ↔ `/category/social-media-prompts/` (plus a 301 redirect bridge on 404).
3. **Section 3 — Connected "How to Use AI Prompts" Workflow (`.ps-workflow-steps`):**
   - Redesigned into a connected 3-step horizontal progression (`01 Select a Matching Style` → `02 Copy & Review Notes` → `03 Paste & Tailor Details`) with custom SVG icons, actionable photographic instructions, and an integrated **Cross-Tool Interpretation Note** explaining honestly that different AI generators interpret the same prompt wording differently.
4. **Section 4 — Asymmetric "Why Choose PromptSyra?" Editorial Showcase (`.ps-why-bento`):**
   - **01 / Clear, Copy-Ready Prompt Formatting:** Features an anatomical 4-part prompt specimen (`Subject`, `Framing`, `Lighting`, `Backdrop`) with a **working `Copy Sample Prompt` button** (`[data-ps-sample-copy]` in `assets/js/promptsyra-home.js` with `aria-live` feedback).
   - **02 / Ideas for Different Photo Styles:** 2×2 visual style matrix covering *Portrait Photography*, *Creative Compositions*, *Seasonal Concepts*, and *Social Media Visuals*.
   - **03 / Helpful Customization Guidance:** Structured 5-factor reference ledger (*Subject details*, *Lighting*, *Background*, *Framing*, *Aspect ratio*) plus a concise practical tip.

## Preserved Functionality
- All surrounding homepage sections (Header, Hero Search & Visual Collage, `WP_Query` Featured AI Prompts Grid, Conditional Community Channels, FAQ Accordion + `FAQPage` JSON-LD, Syra Ansari + 3 Editorial Authors, and Footer), Single-Post UI (`v2.2.0`), and Essential Pages & Authors (`v2.3.0`) remain 100% preserved.

## Upgrading `PromptSyra` to `v2.4.0`
1. **Security First:** Rotate any WordPress administrator password previously shared in plain text (`Users → Profile → New Password`).
2. **Backup First:** Export or retain your previous `promptsyra-v2.3.0.zip` archive (recoverable snapshots are also bundled inside `legacy-backup/`).
3. **Upload & Replace Active Theme:**
   - In WordPress Admin, open `Appearance → Themes → Add New Theme → Upload Theme`, select **`promptsyra-v2.4.0.zip`**, click **Install Now**, and click **Replace active with uploaded**.
   - Clear any WordPress/Cloudflare page cache so `assets/css/promptsyra-home.css?ver=2.4.0` and `assets/js/promptsyra-home.js?ver=2.4.0` serve immediately.
