import SwiftUI

/// Centralized color palette and design tokens for Jibex — a restrained,
/// native-feeling Apple aesthetic. One accent color; status colors are used
/// sparingly (icon + label only), never as heavy tints or glows.
enum JibexTheme {
    // Single accent, used deliberately and sparingly.
    static let brand = Color(red: 0.0, green: 0.34, blue: 0.98)   // iOS system-blue tone

    // Status colors — small, restrained accents paired with icon + label.
    static let delivered = Color(red: 0.20, green: 0.60, blue: 0.35)
    static let pending = Color(red: 0.80, green: 0.55, blue: 0.10)
    static let failed = Color(red: 0.80, green: 0.22, blue: 0.20)
    static let pickup = Color(red: 0.45, green: 0.38, blue: 0.75)

    // Surfaces — native system grouping, not custom tints.
    static func canvas(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .systemGroupedBackground)
    }

    static func cardSurface(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .secondarySystemGroupedBackground)
    }

    static func subtleFill(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .tertiarySystemFill)
    }

    static func hairline(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .separator)
    }

    static func primaryText(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .label)
    }

    static func secondaryText(_ scheme: ColorScheme) -> Color {
        Color(uiColor: .secondaryLabel)
    }

    // Radii
    static let radiusLarge: CGFloat = 22
    static let radiusMedium: CGFloat = 16
    static let radiusSmall: CGFloat = 12
}

/// Reusable Liquid Glass background modifier for chrome elements (tab bars, floating buttons).
/// Uses the real iOS 26 `.glassEffect()` material where available, falling back to a
/// hand-tuned `.ultraThinMaterial` treatment on earlier OS versions.
/// Never applied to body text/content surfaces.
struct LiquidGlassBackground: ViewModifier {
    var cornerRadius: CGFloat = JibexTheme.radiusLarge
    var interactive: Bool = false
    var tint: Color? = nil

    func body(content: Content) -> some View {
        if #available(iOS 26.0, *) {
            let shape = RoundedRectangle(cornerRadius: cornerRadius, style: .continuous)
            let base: Glass = tint != nil ? .regular.tint(tint!.opacity(0.5)) : .regular
            let glass = interactive ? base.interactive() : base
            content.glassEffect(glass, in: shape)
        } else {
            content
                .background(
                    RoundedRectangle(cornerRadius: cornerRadius, style: .continuous)
                        .fill(.ultraThinMaterial)
                        .overlay(
                            RoundedRectangle(cornerRadius: cornerRadius, style: .continuous)
                                .stroke(Color.primary.opacity(0.08), lineWidth: 0.75)
                        )
                        .shadow(color: .black.opacity(0.12), radius: 14, x: 0, y: 6)
                )
        }
    }
}

extension View {
    func liquidGlass(
        cornerRadius: CGFloat = JibexTheme.radiusLarge,
        interactive: Bool = false,
        tint: Color? = nil
    ) -> some View {
        modifier(LiquidGlassBackground(cornerRadius: cornerRadius, interactive: interactive, tint: tint))
    }
}
