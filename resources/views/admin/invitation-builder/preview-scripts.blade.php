<script>
let isOpen = false;
let templateNumber = {{ (int) $template }};

function openEnvelope() {
	if (isOpen) return;

	const modalTemplates = [2, 3, 4, 5];
	if (modalTemplates.indexOf(templateNumber) !== -1) {
		isOpen = true;
		const openButton = document.querySelector('.open-button');
		if (openButton) openButton.style.display = 'none';
		return;
	}

	isOpen = true;

	const inviteAudio = document.getElementById('inviteOpeningAudio');
	if (inviteAudio) {
		inviteAudio.volume = 0.55;
		inviteAudio.play().catch(function () {});
	}

	const isMobile = window.innerWidth <= 768;
	const isSmallMobile = window.innerWidth <= 480;
	const isExtraSmallMobile = window.innerWidth <= 360;
	const isLandscape = window.innerHeight < window.innerWidth;

	let wrapperY, wrapperScale, cardScale;

	if (isExtraSmallMobile) {
		wrapperY = isLandscape ? '5%' : '8%';
		wrapperScale = 0.8;
		cardScale = 0.65;
	} else if (isSmallMobile) {
		wrapperY = isLandscape ? '8%' : '10%';
		wrapperScale = 0.82;
		cardScale = 0.68;
	} else if (isMobile) {
		wrapperY = isLandscape ? '10%' : '12%';
		wrapperScale = 0.85;
		cardScale = 0.7;
	} else {
		wrapperY = '10%';
		wrapperScale = 0.85;
		cardScale = 0.7;
	}

	const tl = new TimelineMax();

	tl.to('.invitation-wrapper', 0.6, {
			scale: wrapperScale,
			y: wrapperY,
			ease: Power2.easeInOut,
		})
		.to('.open-button', 0.4, {
			opacity: 0,
			y: '180px',
			pointerEvents: 'none',
			ease: Power2.easeInOut,
		}, '-=0.4')
		.to('.flap, .template1-flap, .template16-flap, .template2-flap, .template3-flap, .template4-flap, .template5-flap', 0.8, {
			rotationX: 180,
			zIndex: 1,
			ease: Power2.easeInOut,
		}, '-=0.2')
		.to('.mask', 1.0, {
			clipPath: 'inset(0 0 0% 0)',
			ease: Power2.easeInOut,
		}, '-=0.5')
		.to('.card', 1.0, {
			y: '-65%',
			scale: cardScale,
			zIndex: 10,
			ease: Power4.easeOut,
			onUpdate: function () {
				const progress = this.progress();
				const currentY = 100 - progress * 300;
				if (currentY <= 0) {
					const card = document.querySelector('.card');
					if (card) {
						card.style.opacity = '1';
						card.style.visibility = 'visible';
					}
				}
			},
			onComplete: () => {
				const mask = document.querySelector('.mask');
				if (mask) mask.style.setProperty('z-index', '5');
			},
		}, '+=0.2')
		.to('.face', 0.6, {
			boxShadow: '0 15px 40px rgba(18, 18, 35, 0.6), 0 8px 20px rgba(0, 0, 0, 0.4)',
			ease: Power2.easeOut,
		}, '-=0.8')
		.to('.card', 1.6, { y: '-20%', ease: Power3.easeOut }, '+=0.3')
		.to('.face', 0.8, {
			boxShadow: '0 20px 50px rgba(18, 18, 35, 0.7), 0 12px 30px rgba(0, 0, 0, 0.5)',
			ease: Power2.easeOut,
		}, '-=1.2')
		.to('.card', 0.4, { y: '0%', scale: 1.02, ease: Power2.easeOut }, '-=0.4')
		.to('.card', 0.6, { scale: 1, ease: Power2.easeOut });
}

function toggleCard() {
	if (!isOpen) return;
	const card = document.querySelector('.card');
	if (!card) return;
	const flipped = card.dataset.flipped === '1';
	card.dataset.flipped = flipped ? '0' : '1';
	TweenMax.to('.card', 0.8, {
		rotationY: flipped ? 0 : 180,
		ease: Power3.easeInOut,
		transformPerspective: 1000,
	});
}
</script>
