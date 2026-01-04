	<script>
	let isOpen = false;
	let isFlipped = false;
	let currentView = "envelope";
	let templateNumber = {{$template ?? 1}};

	function openEnvelope() {
		if (isOpen) return;

		// For templates 2-5 (modal-style), skip envelope animation
		if (templateNumber > 1) {
			isOpen = true;
			const modalWrapper = document.querySelector(
				'.template2-modal-wrapper, .template3-modal-wrapper, .template4-modal-wrapper, .template5-modal-wrapper'
			);
			if (modalWrapper) {
				// Modal is already visible with animation, just hide the open button
				const openButton = document.querySelector(".open-button");
				if (openButton) {
					openButton.style.display = "none";
				}
				return;
			}
			return;
		}

		// Template 1 uses envelope animation
		isOpen = true;

		isOpen = true;

		// Enhanced device detection
		const isMobile = window.innerWidth <= 768;
		const isSmallMobile = window.innerWidth <= 480;
		const isExtraSmallMobile = window.innerWidth <= 360;
		const isLandscape = window.innerHeight < window.innerWidth;

		// Dynamic positioning based on device type
		let wrapperY, wrapperScale, cardScale;

		if (isExtraSmallMobile) {
			wrapperY = isLandscape ? "5%" : "8%";
			wrapperScale = 0.8;
			cardScale = 0.65;
		} else if (isSmallMobile) {
			wrapperY = isLandscape ? "8%" : "10%";
			wrapperScale = 0.82;
			cardScale = 0.68;
		} else if (isMobile) {
			wrapperY = isLandscape ? "10%" : "12%";
			wrapperScale = 0.85;
			cardScale = 0.7;
		} else {
			wrapperY = "10%";
			wrapperScale = 0.85;
			cardScale = 0.7;
		}

		// Create timeline for smooth coordinated animation
		const tl = new TimelineMax();

		// Stage 1: Scale down wrapper and hide button
		tl.to(".invitation-wrapper", 0.6, {
				scale: wrapperScale,
				y: wrapperY,
				ease: Power2.easeInOut,
			})
			.to(".open-button", 0.4, {
				opacity: 0,
				y: "180px",
				pointerEvents: "none",
				ease: Power2.easeInOut,
			}, "-=0.4")

			// Stage 2: Open flap (works with all template flaps)
			.to(".flap, .template1-flap, .template2-flap, .template3-flap, .template4-flap, .template5-flap",
				0.8, {
					rotationX: 180,
					zIndex: 1,
					ease: Power2.easeInOut,
				}, "-=0.2")

			// Stage 3: Reveal the mask area (envelope opens) - fully open for card visibility
			.to(".mask", 1.0, {
				clipPath: "inset(0 0 0% 0)",
				ease: Power2.easeInOut,
			}, "-=0.5")

			// Stage 4: Card slides up from envelope (invisible until it passes 0%)
			.to(".card", 1.0, {
				y: "-65%",
				scale: cardScale,
				zIndex: 10,
				ease: Power4.easeOut,
				onUpdate: function() {
					// Show card only when it passes 0% y-axis
					const progress = this.progress();
					const currentY = 100 - (progress *
						300); // 120% to -40% = 160% range

					if (currentY <= 0) {
						const card = document.querySelector(".card");
						card.style.opacity = "1";
						card.style.visibility = "visible";
					}
				},
				onComplete: () => {
					const mask = document.querySelector(".mask");
					mask.style.setProperty("z-index", "5");
				}
			}, "+=0.2")

			// Stage 6: Enhanced card box shadow during extraction
			.to(".face", 0.6, {
				boxShadow: "0 15px 40px rgba(18, 18, 35, 0.6), 0 8px 20px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)",
				ease: Power2.easeOut,
			}, "-=0.8")

			// Stage 7: Smooth descent to final position with subtle bounce
			.to(".card", 1.6, {
				y: "-20%",
				ease: Power3.easeOut,
			}, "+=0.3")

			// Stage 8: Final settling effect with enhanced glow
			.to(".face", 0.8, {
				boxShadow: "0 20px 50px rgba(18, 18, 35, 0.7), 0 12px 30px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.15)",
				ease: Power2.easeOut,
			}, "-=1.2")

			// Stage 9: Subtle scale pulse for final emphasis
			.to(".card", 0.4, {
				y: "0%",
				scale: 1.02,
				ease: Power2.easeOut,
			}, "-=0.4")
			.to(".card", 0.6, {
				scale: 1,
				ease: Power2.easeOut,
			});
	}

	function toggleCard() {
		if (!isOpen) return;

		const rotationY = isFlipped ? 0 : 180;
		isFlipped = !isFlipped;

		TweenMax.to(".card", 0.8, {
			rotationY: rotationY,
			ease: Power3.easeInOut,
			transformPerspective: 1000,
		});
	}

	function acceptInvitation() {
		// Show loading state
		const acceptBtn = document.querySelector('.btn-accept');
		const originalText = acceptBtn.innerHTML;
		acceptBtn.innerHTML =
			'<span style="display:inline-block;width:20px;height:20px;border:2px solid #fff;border-radius:50%;border-top:2px solid transparent;animation:spin 1s linear infinite;"></span>';
		acceptBtn.disabled = true;

		// // Simulate API call delay for better UX
		// setTimeout(() => {
		//   currentView = "success";
		//   showView("successView");
		// }, 500);

		// Uncomment below for actual API integration
		fetch('{{ $routes["accept"] }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					currentView = "success";
					showView("successView");
				} else {
					alert(data.message);
					acceptBtn.innerHTML = originalText;
					acceptBtn.disabled = false;
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('حدث خطأ أثناء قبول الدعوة');
				acceptBtn.innerHTML = originalText;
				acceptBtn.disabled = false;
			});
	}

	function declineInvitation() {
		// Show loading state
		const declineBtn = document.querySelector('.btn-decline');
		const originalText = declineBtn.innerHTML;
		declineBtn.innerHTML =
			'<span style="display:inline-block;width:20px;height:20px;border:2px solid #ef4444;border-radius:50%;border-top:2px solid transparent;animation:spin 1s linear infinite;"></span>';
		declineBtn.disabled = true;

		// // Simulate API call delay for better UX
		// setTimeout(() => {
		//   currentView = "decline";
		//   showView("declineView");
		// }, 500);

		// Uncomment below for actual API integration
		fetch('{{ $routes["decline"] }}', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					currentView = "decline";
					showView("declineView");
				} else {
					alert(data.message || 'حدث خطأ أثناء رفض الدعوة');
					declineBtn.innerHTML = originalText;
					declineBtn.disabled = false;
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('حدث خطأ أثناء رفض الدعوة');
				declineBtn.innerHTML = originalText;
				declineBtn.disabled = false;
			});
	}

	function goBack() {
		currentView = "envelope";
		showView("envelopeView");

		// Reset envelope state
		isOpen = false;
		isFlipped = false;

		// Create smooth reset animation
		const resetTl = new TimelineMax();

		// Stage 1: Reset card scale and shadow first
		resetTl
			.to(".card", 0.4, {
				scale: 0.95,
				ease: Power2.easeOut,
			})
			.to(".face", 0.4, {
				boxShadow: "0 8px 25px rgba(18, 18, 35, 0.4), 0 4px 12px rgba(0, 0, 0, 0.3)",
				ease: Power2.easeOut,
			}, "-=0.4")

			// Stage 2: Slide card back into envelope
			.to(".card", 0.8, {
				y: "120%",
				rotationY: 0,
				opacity: 0,
				visibility: "hidden",
				zIndex: 0,
				ease: Power3.easeIn,
				onStart: () => {
					const mask = document.querySelector(".mask");
					mask.style.setProperty("z-index", "1");
				}
			}, "+=0.2")

			// Stage 3: Close the mask (envelope closes)
			.to(".mask", 0.6, {
				clipPath: "inset(0 0 85% 0)",
				ease: Power2.easeInOut,
			}, "-=0.4")

			// Stage 4: Close the flap (works with all template flaps)
			.to(".flap, .template1-flap, .template2-flap, .template3-flap, .template4-flap, .template5-flap",
				0.6, {
					rotationX: 0,
					zIndex: 4,
					ease: Power2.easeInOut,
				}, "-=0.4")

			// Stage 5: Scale wrapper back to normal and reset position
			.to(".invitation-wrapper", 0.6, {
				scale: 1,
				y: "0%",
				ease: Power2.easeInOut,
			}, "-=0.5")

			// Stage 6: Bring back the open button
			.to(".open-button", 0.4, {
				opacity: 1,
				y: "0px",
				pointerEvents: "auto",
				ease: Power2.easeInOut,
			}, "-=0.2");
	}

	function showView(viewId) {
		// Hide all views
		document
			.querySelectorAll("#envelopeView, #successView, #declineView")
			.forEach((view) => {
				view.classList.add("hidden");
				view.classList.remove("active");
			});

		// Show target view
		const targetView = document.getElementById(viewId);
		targetView.classList.remove("hidden");

		if (viewId !== "envelopeView") {
			setTimeout(() => {
				targetView.classList.add("active");
			}, 50);
		}
	}

	// Touch event handling for mobile devices
	function addTouchSupport() {
		const buttons = document.querySelectorAll('.btn, .open-button, .flip-button, .back-button');

		buttons.forEach(button => {
			// Add touch feedback
			button.addEventListener('touchstart', function(e) {
				this.style.transform = (this.style.transform ||
					'') + ' scale(0.95)';
			}, {
				passive: true
			});

			button.addEventListener('touchend', function(e) {
				this.style.transform = this.style.transform
					.replace(' scale(0.95)', '');
			}, {
				passive: true
			});

			button.addEventListener('touchcancel', function(e) {
				this.style.transform = this.style.transform
					.replace(' scale(0.95)', '');
			}, {
				passive: true
			});
		});
	}

	// Responsive adjustments on orientation change
	function handleOrientationChange() {
		// Add a small delay to allow for orientation change to complete
		setTimeout(() => {
			if (isOpen && currentView === "envelope") {
				// Recalculate positions for new orientation
				const isMobile = window.innerWidth <= 768;
				const isLandscape = window.innerHeight < window.innerWidth;

				if (isMobile) {
					const newWrapperY = isLandscape ? "5%" : "12%";
					TweenMax.set(".invitation-wrapper", {
						y: newWrapperY
					});
				}
			}
		}, 300);
	}

	// Prevent zoom on double tap for iOS
	function preventDoubleTapZoom() {
		let lastTouchEnd = 0;
		document.addEventListener('touchend', function(event) {
			const now = (new Date()).getTime();
			if (now - lastTouchEnd <= 300) {
				event.preventDefault();
			}
			lastTouchEnd = now;
		}, false);
	}

	// Initialize
	document.addEventListener("DOMContentLoaded", function() {
		const initialView = "{{ $initialView }}";
		console.log("initialView", initialView);

		// For templates 2-5 (modal-style), they show immediately with CSS animations
		// Template 1 uses envelope animation and needs the open button
		if (templateNumber > 1) {
			const modalWrapper = document.querySelector(
				'.template2-modal-wrapper, .template3-modal-wrapper, .template4-modal-wrapper, .template5-modal-wrapper'
			);
			if (modalWrapper) {
				isOpen = true;
				// Hide open button if it exists for modal templates
				const openButton = document.querySelector(".open-button");
				if (openButton) {
					openButton.style.display = "none";
				}
			}
		}
		// Template 1 keeps the envelope design with open button visible

		if (initialView === "success") {
			currentView = "success";
			showView("successView");
		} else if (initialView === "decline") {
			currentView = "decline";
			showView("declineView");
		} else {
			currentView = "envelope";
			showView("envelopeView");
		}

		addTouchSupport();
		preventDoubleTapZoom();
	});

	// Handle orientation changes
	window.addEventListener('orientationchange', handleOrientationChange);
	window.addEventListener('resize', handleOrientationChange);

	// Open location function
	function openLocation() {
		const latitude = {{$invitation->latitude ?? 'null'}};
		const longitude = {{$invitation->longitude ?? 'null'}};

		if (latitude && longitude) {
			// Check if user is on mobile
			const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
				.userAgent);

			if (isMobile) {
				// For mobile devices, try to open the native map app
				if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
					// iOS - try to open Apple Maps first, fallback to Google Maps
					const appleMapsUrl = `maps://maps.apple.com/?q=${latitude},${longitude}`;
					const googleMapsUrl = `https://maps.google.com/?q=${latitude},${longitude}`;

					// Try Apple Maps first
					window.location.href = appleMapsUrl;

					// Fallback to Google Maps after a short delay if Apple Maps didn't work
					setTimeout(() => {
						window.open(googleMapsUrl, '_blank');
					}, 500);
				} else {
					// Android and other mobile devices - use Google Maps
					const googleMapsUrl = `https://maps.google.com/?q=${latitude},${longitude}`;
					window.open(googleMapsUrl, '_blank');
				}
			} else {
				// For desktop, open Google Maps in a new tab
				const googleMapsUrl = `https://maps.google.com/?q=${latitude},${longitude}`;
				window.open(googleMapsUrl, '_blank');
			}
		} else {
			// If no coordinates available, try to search by address
			const address = encodeURIComponent('{{ $invitation->address ?? "" }}');
			if (address) {
				const googleMapsUrl = `https://maps.google.com/?q=${address}`;
				window.open(googleMapsUrl, '_blank');
			} else {
				alert('موقع الحدث غير متوفر');
			}
		}
	}

	function openMediaInNewTab() {
		const mediaUrl = '{{ $invitation->image() }}';
		if (mediaUrl) {
			window.open(mediaUrl, '_blank');
		}
	}
	</script>