import SwiftUI

/// Circular icon button matching the top-bar bell style — used for shared
/// header actions like the scanner button across Home, Runsheets, and Pickups.
struct HeaderIconButton: View {
    let systemImage: String
    var action: () -> Void = {}

    @Environment(\.colorScheme) private var scheme

    var body: some View {
        Button(action: action) {
            Image(systemName: systemImage)
                .foregroundStyle(JibexTheme.primaryText(scheme))
                .font(.system(size: 16, weight: .medium))
                .frame(width: 42, height: 42)
                .liquidGlass(cornerRadius: 21, interactive: true)
        }
        .buttonStyle(.plain)
    }
}
