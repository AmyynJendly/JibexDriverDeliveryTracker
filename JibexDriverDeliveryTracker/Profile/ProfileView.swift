import SwiftUI

struct ProfileView: View {
    @Environment(AppearanceManager.self) private var appearance
    @Environment(\.colorScheme) private var scheme

    @State private var biometricEnabled = true
    @State private var showLogoutConfirm = false

    var body: some View {
        NavigationStack {
            ScrollView {
                VStack(spacing: 22) {
                    profileHeader
                    appearanceSection
                    securitySection
                    appSection
                    logoutButton
                }
                .padding(.horizontal, 18)
                .padding(.top, 12)
                .padding(.bottom, 120)
            }
            .background(JibexTheme.canvas(scheme).ignoresSafeArea())
            .navigationTitle("Profile")
            .confirmationDialog("Log out of Jibex?", isPresented: $showLogoutConfirm, titleVisibility: .visible) {
                Button("Log Out", role: .destructive) {}
                Button("Cancel", role: .cancel) {}
            }
        }
    }

    private var profileHeader: some View {
        VStack(spacing: 10) {
            ZStack {
                Circle()
                    .fill(JibexTheme.brand.opacity(0.12))
                    .frame(width: 84, height: 84)
                Text("MJ")
                    .font(.system(.title2, design: .rounded).weight(.bold))
                    .foregroundStyle(JibexTheme.brand)
            }
            Text("Marcus Johnson")
                .font(.headline)
                .foregroundStyle(JibexTheme.primaryText(scheme))
            Text("@marcus.driver")
                .font(.caption)
                .foregroundStyle(JibexTheme.secondaryText(scheme))
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, 8)
    }

    private var appearanceSection: some View {
        SettingsSection(title: "Appearance") {
            SettingsRow(
                icon: "moon.fill",
                iconColor: JibexTheme.pickup,
                title: "Dark Mode",
                subtitle: appearance.isDarkMode ? "On" : "Off"
            ) {
                Toggle("", isOn: Binding(
                    get: { appearance.isDarkMode },
                    set: { appearance.isDarkMode = $0 }
                ))
                .labelsHidden()
                .tint(JibexTheme.brand)
            }
            .padding(.vertical, 8)
        }
    }

    private var securitySection: some View {
        SettingsSection(title: "Security") {
            SettingsRow(
                icon: "faceid",
                iconColor: JibexTheme.delivered,
                title: "Biometric Login",
                subtitle: biometricEnabled ? "Enabled" : "Disabled"
            ) {
                Toggle("", isOn: $biometricEnabled)
                    .labelsHidden()
                    .tint(JibexTheme.brand)
            }
            .padding(.vertical, 8)

            SettingsRowDivider()

            Button {} label: {
                SettingsRow(icon: "lock.fill", iconColor: JibexTheme.brand, title: "Change Password") {
                    Image(systemName: "chevron.right")
                        .font(.caption.weight(.semibold))
                        .foregroundStyle(JibexTheme.secondaryText(scheme))
                }
                .padding(.vertical, 8)
            }
            .buttonStyle(.plain)
        }
    }

    private var appSection: some View {
        SettingsSection(title: "App") {
            Button {} label: {
                SettingsRow(icon: "gearshape.fill", iconColor: JibexTheme.secondaryText(scheme), title: "Settings") {
                    Image(systemName: "chevron.right")
                        .font(.caption.weight(.semibold))
                        .foregroundStyle(JibexTheme.secondaryText(scheme))
                }
                .padding(.vertical, 8)
            }
            .buttonStyle(.plain)

            SettingsRowDivider()

            SettingsRow(icon: "info.circle.fill", iconColor: JibexTheme.secondaryText(scheme), title: "Version") {
                Text("2.4.1")
                    .font(.footnote.weight(.medium))
                    .foregroundStyle(JibexTheme.secondaryText(scheme))
            }
            .padding(.vertical, 8)
        }
    }

    private var logoutButton: some View {
        Button {
            showLogoutConfirm = true
        } label: {
            HStack {
                Image(systemName: "rectangle.portrait.and.arrow.right")
                    .font(.system(size: 15, weight: .semibold))
                Text("Log Out")
                    .font(.subheadline.weight(.semibold))
            }
            .foregroundStyle(JibexTheme.failed)
            .frame(maxWidth: .infinity)
            .frame(height: 50)
            .background(
                RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                    .fill(JibexTheme.failed.opacity(scheme == .dark ? 0.16 : 0.1))
                    .overlay(
                        RoundedRectangle(cornerRadius: JibexTheme.radiusMedium, style: .continuous)
                            .stroke(JibexTheme.failed.opacity(0.35), lineWidth: 1)
                    )
            )
        }
        .buttonStyle(.plain)
        .padding(.top, 4)
    }
}
