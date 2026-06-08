<style>
.ib-preview-device {
	background: linear-gradient(145deg, #1e1e2e, #2a2a40);
	border-radius: 16px;
	padding: 14px;
	margin: 0 auto;
	transition: max-width 0.25s ease;
}

.ib-preview-device.is-mobile {
	max-width: 390px;
	box-shadow: 0 0 0 3px #333, 0 0 0 6px #1a1a1a;
	border-radius: 28px;
}

.ib-preview-device.is-desktop {
	max-width: 100%;
}

.ib-preview-device iframe {
	width: 100%;
	border: 0;
	display: block;
	background: #0f0f18;
	border-radius: 8px;
}

.ib-preview-device.is-desktop iframe {
	height: min(72vh, 680px);
}

.ib-preview-device.is-mobile iframe {
	height: min(68vh, 640px);
	border-radius: 12px;
}

.ib-preview-loading {
	position: absolute;
	inset: 0;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(255, 255, 255, 0.85);
	border-radius: 12px;
	z-index: 2;
}

.ib-preview-loading.d-none {
	display: none !important;
}
</style>
