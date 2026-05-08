# SEMA FTI UAJY - Agent Documentation

## Overview

This document outlines the current state of the Web_SEMA website and serves as a reference for future development, maintenance, and enhancements.

**Website:** Senat Mahasiswa (SEMA) Fakultas Teknologi Industri UAJY  
**Last Updated:** May 2026  
**Status:** Design Phase 2 - Minimal Clean Aesthetic

## Current Architecture

### Frontend Stack

- **HTML5** - Semantic markup
- **Tailwind CSS v3** - via CDN (no custom plugins)
- **JavaScript (Vanilla)** - Lightweight interactions, no frameworks
- **Responsive Design** - Mobile-first approach

### Backend Stack

- **PHP Native** - No frameworks
- **MySQL/MariaDB** - Database
- **mysqli** - Database driver (prepared statements for security)

## Design System (Phase 2 - Current)

### Color Palette

Simplified from neon/multi-color to clean minimal aesthetic:

- **Primary Background:** `#050505` (night)
- **Secondary Background:** `#0B0B0F` (midnight)
- **Single Accent Color:** `#0FD7D6` (accent/cyan)
- **Text Hierarchy:** White, white/70%, white/60%, white/40%

### Typography

- **Font Family:** Space Grotesk (sans-serif only)
- **No Serif Fonts:** Removed Playfair Display serif font
- **Font Weights:** 400 (body), 500 (labels), 600 (headings), 700 (display)

### Components

- **Clean Panels:** `bg-white/2% border-white/10%` (replaced glassmorphism)
- **Card Styling:** Minimal borders, subtle background, simple hover effects
- **Buttons:** Solid accent color with opacity effects
- **Forms:** Simple input styling with accent focus states

## File Structure

```
Web_SEMA/
├── index.php                   # Landing page (fully redesigned)
├── bidang-detail.php          # Division detail page (partially updated)
├── config/
│   └── database.php           # DB connection
├── admin/
│   ├── dashboard.php          # Admin home
│   ├── login.php              # Authentication
│   ├── divisi/
│   │   └── index.php          # Manage divisions
│   ├── event/
│   │   └── index.php          # Manage events
│   ├── member/
│   │   └── index.php          # Manage members
│   └── proker/
│       └── index.php          # Manage programs
├── asset/
│   ├── img/                   # Images & icons
│   └── uploads/proker/        # Uploaded program photos
├── README.md                  # User documentation
└── agents.md                  # This file
```

## Key Features

### Homepage (index.php)

1. **Hero Section** - Full-width with auto-sliding background images
   - 3-image carousel rotating every 5 seconds
   - Centered overlay text and CTA buttons
   - Image sources: seminar backdrop, spfest, baksos

2. **Bidang (Division) Cards** - 5 divisions displayed
   - Each card has icon, title, description, bullet points, learn more link
   - Single accent color for consistency

3. **Komunitas (Communities) Section** - 6+ community cards
   - Simplified compact cards with minimal styling
   - Community icons and brief descriptions

4. **Berita (News) Section** - Latest events from database
   - Displays 3 most recent events
   - Optional event image fallback

5. **Tentang (About) Section** - Statistics and company values
   - 4 stat cards showing key metrics
   - Highlights values and achievements

6. **Kontak (Contact) Section** - Contact information and form
   - Contact details with icons
   - Functional contact form with client-side validation

7. **Footer** - Navigation and social links

### Admin Dashboard

- **Session-based authentication**
- **CRUD operations** for divisi, event, member, proker
- **File upload support** with validation
- **Responsive sidebar navigation**

## Recent Changes (Phase 2 Redesign)

### What Changed

1. ✅ Removed Playfair Display serif font - now using Space Grotesk throughout
2. ✅ Simplified color palette - from 6+ colors to single accent color
3. ✅ Redesigned hero section - full-width with auto-sliding images
4. ✅ Removed glassmorphism effects - replaced with clean panels
5. ✅ Centered hero text and CTA buttons
6. ✅ Simplified bidang cards - reduced visual complexity
7. ✅ Streamlined komunitas cards - more compact
8. ✅ Updated kontak form styling
9. ✅ Updated footer styling and colors

### What's Maintained

- ✅ All PHP logic and database queries
- ✅ All existing functionality (CRUD, forms, auth)
- ✅ Database structure (no schema changes)
- ✅ Security practices (prepared statements, input validation)
- ✅ Responsive breakpoints
- ✅ Accessibility features

## Database Schema

### Tables

- **divisi** - 5 divisions (Pengurus Harian, Minat Bakat, Sosial Masyarakat, Usaha Dana, Kominfo)
- **event** - Events/news items
- **proker** - Programs per division
- **member** - Division members with photos
- **admin** - Admin users

## JavaScript Features

### Hero Slider

- Auto-advances every 5 seconds
- Clickable dot indicators for manual navigation
- Smooth CSS transitions (700ms duration)

### Loading Screen

- Shows while page loads
- Animates progress bar
- Fades out when complete

### Mobile Menu

- Toggle menu for mobile devices
- Overlay backdrop
- Smooth slide animation

### Contact Form

- Real-time button feedback
- Success animation
- Auto-reset after submission

### Smooth Scrolling

- All anchor links use smooth scroll
- Fixed header offset accounting

## Future Enhancements

### Priority 1 (High)

- [ ] Complete bidang-detail.php redesign with new minimal styling
- [ ] Update all admin pages (5 files) with consistent minimal theme
- [ ] Test responsive design on all breakpoints
- [ ] Implement CMS for content management

### Priority 2 (Medium)

- [ ] Add member photo upload functionality in admin
- [ ] Implement event image optimization
- [ ] Add search functionality for events/programs
- [ ] Create admin statistics dashboard with charts

### Priority 3 (Low)

- [ ] Dark mode toggle (optional)
- [ ] Multi-language support (ID/EN)
- [ ] Add testimonials section
- [ ] Implement newsletter signup
- [ ] Add blog/article section

## Development Guidelines

### Style Guidelines

- Use only Space Grotesk font family
- Use single accent color (#0FD7D6) for highlights
- Prefer clean-panel class over glass-panel
- Use white/ opacities for text hierarchy (80%, 70%, 60%, 40%)

### Component Patterns

```html
<!-- Card Pattern -->
<div class="card-hover clean-panel rounded-lg p-6">
  <div
    class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center mb-4"
  >
    <i class="fas fa-icon text-accent"></i>
  </div>
  <h3 class="font-semibold mb-2">Title</h3>
  <p class="text-white/70 text-sm mb-3">Description</p>
</div>

<!-- Button Pattern -->
<button
  class="px-6 py-3 bg-accent text-night font-semibold rounded-lg hover:opacity-90 transition-opacity"
>
  Action
</button>

<!-- Form Pattern -->
<input
  type="text"
  class="w-full px-4 py-2 bg-white/5 border border-white/10 rounded-lg text-white placeholder-white/40 focus:border-accent focus:outline-none transition-colors"
/>
```

### Database Queries

- Always use prepared statements
- Validate and sanitize all inputs
- Use mysqli\_\* functions consistently
- Include error handling

### File Uploads

- Validate file types and sizes
- Store in dedicated upload directory
- Use secure filenames with timestamps
- Implement cleanup for old files

## Performance Notes

- **Page Load:** ~2-3 seconds (with loading screen)
- **Hero Images:** Optimized JPG format, ~50-100KB each
- **CSS:** Via CDN (no build step required)
- **JS:** All vanilla, minimal dependencies

## Security Considerations

- ✅ Prepared statements for SQL queries
- ✅ Input validation with htmlspecialchars()
- ✅ Session-based admin authentication
- ✅ FILTER_VALIDATE_INT for sensitive inputs
- ⚠️ Recommended: Add CSRF tokens to forms
- ⚠️ Recommended: Implement rate limiting for admin login

## Support & Troubleshooting

### Hero Slider Not Animating

- Check if `heroSlider` element exists in HTML
- Verify JavaScript runs after DOM load
- Check browser console for errors

### Images Not Loading

- Verify paths are relative from index.php
- Check asset/img/ folder exists
- Ensure file permissions are readable

### Database Connection Issues

- Verify config/database.php settings
- Check MySQL service is running
- Confirm user credentials

### Styling Not Applied

- Clear browser cache (Ctrl+Shift+Del)
- Check Tailwind CDN loading
- Verify no CSS conflicts

## Contact & Maintenance

**Maintained By:** KOMINFO SEMA FTI UAJY  
**Last Maintained:** May 2026  
**Next Review:** August 2026

For issues, feature requests, or maintenance needs, contact the KOMINFO team through official SEMA channels.

---

**Note:** This document is meant for developers and maintainers. Keep it updated as the website evolves.
