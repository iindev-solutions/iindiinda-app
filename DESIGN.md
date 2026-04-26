---
version: alpha
name: iindiinda
description: Dark-first Telegram Mini App design system for local coordination services
colors:
  primary: "#5edac6"
  background: "#0a0c0e"
  surface: "#12161a"
  surfaceElevated: "#171c21"
  surfaceSoft: "#27313a"
  textPrimary: "#f3f6f8"
  textSecondary: "#9aa6b2"
  success: "#22c55e"
  warning: "#f59e0b"
  danger: "#ef4444"
typography:
  display:
    fontFamily: Geist
    fontSize: 1.75rem
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: -0.02em
  title:
    fontFamily: Geist
    fontSize: 1.125rem
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: -0.01em
  body:
    fontFamily: Inter
    fontSize: 0.95rem
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: Inter
    fontSize: 0.8125rem
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: 0.01em
  button:
    fontFamily: Inter
    fontSize: 0.9375rem
    fontWeight: 600
    lineHeight: 1.2
rounded:
  sm: 10px
  md: 14px
  lg: 18px
  xl: 24px
spacing:
  xs: 4px
  sm: 8px
  md: 12px
  lg: 16px
  xl: 24px
  xxl: 32px
components:
  pageShell:
    backgroundColor: "{colors.background}"
    textColor: "{colors.textPrimary}"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.textPrimary}"
    rounded: "{rounded.lg}"
    padding: "{spacing.lg}"
  cardElevated:
    backgroundColor: "{colors.surfaceElevated}"
    textColor: "{colors.textPrimary}"
    rounded: "{rounded.xl}"
    padding: "{spacing.xl}"
  buttonPrimary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.background}"
    typography: "{typography.button}"
    rounded: "{rounded.md}"
    height: 48px
  buttonSecondary:
    backgroundColor: "{colors.surfaceSoft}"
    textColor: "{colors.textPrimary}"
    typography: "{typography.button}"
    rounded: "{rounded.md}"
    height: 48px
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.textPrimary}"
    typography: "{typography.body}"
    rounded: "{rounded.md}"
    height: 48px
  tabActive:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.background}"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
  tabInactive:
    backgroundColor: "{colors.surfaceSoft}"
    textColor: "{colors.textSecondary}"
    typography: "{typography.label}"
    rounded: "{rounded.md}"
  statusOpen:
    backgroundColor: "{colors.warning}"
    textColor: "{colors.background}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
  statusMatched:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.background}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
  statusCompleted:
    backgroundColor: "{colors.success}"
    textColor: "{colors.background}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
  statusCancelled:
    backgroundColor: "{colors.danger}"
    textColor: "{colors.background}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
---

## Overview

iindiinda uses a calm dark UI for Telegram Mini App flows where people need to understand status, next action, and trust signals fast. The interface should feel local, clear, and light on cognitive load rather than flashy or marketplace-heavy.

## Colors

The system uses one accent color only. Cyan is for the main action, active state, and confirmed attention points. Backgrounds stay quiet and dark so lists, cards, and forms remain readable in small mobile WebViews.

Success, warning, and danger exist for lifecycle states, not for decoration. Avoid adding extra accent hues unless a new product rule requires them.

## Typography

Headings use Geist for a slightly stronger product voice. Body and controls use Inter for dense mobile readability. Typography should communicate hierarchy through weight and spacing, not through many font sizes.

Use `display` for page hero or top-level service titles, `title` for cards and section headings, `body` for descriptions and form help, `label` for pills and metadata, and `button` for all CTA text.

## Layout

Build every screen mobile-first around a single narrow column. Default rhythm is compact but not cramped: 16px horizontal page padding, 16px card spacing, and large enough tap targets for Telegram usage.

Lists should scan vertically without noise. Group related actions at the bottom of cards or pages. Prefer one strong primary CTA per screen.

## Elevation & Depth

Depth should come from surface steps, spacing, and border separation rather than heavy shadows. Use `surface` for standard cards and inputs, and `surfaceElevated` for sheets, emphasized panels, and modal-like layers.

## Shapes

Corners are soft and friendly, never sharp. Use medium to large rounding for cards and sheets, and smaller rounding for status pills. Rounded geometry should help the app feel approachable while still structured.

## Components

Shared primitives should lead the redesign before page-specific work. Prioritize page shell, service cards, list cards, tabs, status badges, buttons, and inputs. AYAN and AGAL should look like siblings built from one kit, not separate products.

Cards should keep metadata secondary and make the main intent obvious first: route, request, status, response count, or next action. Forms should look stable inside Telegram and avoid decorative interaction patterns that add fragility.

## Do's and Don'ts

- Do keep one clear visual hierarchy per screen.
- Do make status and next action obvious within one glance.
- Do keep tap targets generous and form controls stable inside Telegram.
- Do reuse the same card, tab, badge, and CTA patterns across services.
- Don't introduce extra accent colors for individual services.
- Don't overload cards with low-priority metadata.
- Don't rely on fancy transitions, popovers, or nested interactions where a simple control works better.
- Don't let service pages drift away from the shared shell and primitive set.
