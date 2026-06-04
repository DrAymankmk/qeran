{{-- Seal shape + palette styles for builder-wedding-envelope --}}
.wi-env-seal .wi-seal-initials {
	position: relative;
	z-index: 2;
	max-width: 85%;
}
.wi-env-seal .wi-seal-ring {
	position: absolute;
	inset: 12%;
	border-radius: 50%;
	border: 2px solid rgba(255, 220, 200, 0.28);
	box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.18), inset 0 0 10px rgba(0, 0, 0, 0.12);
	pointer-events: none;
	z-index: 1;
}
.wi-env-seal:not(.has-seal-ring) .wi-seal-ring { display: none; }

/* Custom color (--s-* set inline) overrides palette defaults */
.wi-env-seal.has-seal-custom-color[class*="wi-seal-shape-wax"]::before,
.wi-env-seal.has-seal-custom-color.wi-seal-shape-floral-round::before,
.wi-env-seal.has-seal-custom-color.wi-seal-shape-emboss-round::before,
.wi-env-seal.has-seal-custom-color.wi-seal-shape-foil-round::before,
.wi-env-seal.has-seal-custom-color.wi-seal-shape-royal-shield::before {
	background:
		radial-gradient(ellipse 35% 28% at 32% 28%, var(--s-hi), transparent 55%),
		radial-gradient(ellipse 80% 70% at 50% 55%, var(--s-mid), var(--s-lo) 100%);
}

/* Palettes */
.wi-env-seal.wi-seal-pal-crimson { --s-hi: rgba(255, 120, 120, 0.45); --s-mid: #a31830; --s-lo: #4a0816; --s-drip: #5c0818; --s-ink: rgba(255, 245, 238, 0.92); }
.wi-env-seal.wi-seal-pal-burgundy { --s-hi: rgba(255, 140, 150, 0.4); --s-mid: #7a1830; --s-lo: #3d0818; --s-drip: #4a0a14; --s-ink: rgba(255, 242, 240, 0.92); }
.wi-env-seal.wi-seal-pal-rose { --s-hi: rgba(255, 180, 200, 0.5); --s-mid: #c44a6a; --s-lo: #7a2040; --s-drip: #8b2848; --s-ink: rgba(255, 250, 252, 0.95); }
.wi-env-seal.wi-seal-pal-navy { --s-hi: rgba(140, 180, 255, 0.35); --s-mid: #1e3a5f; --s-lo: #0f2038; --s-drip: #142a48; --s-ink: rgba(230, 238, 255, 0.92); }
.wi-env-seal.wi-seal-pal-emerald { --s-hi: rgba(140, 255, 200, 0.35); --s-mid: #1a6b4a; --s-lo: #0d3d28; --s-drip: #124830; --s-ink: rgba(240, 255, 248, 0.92); }
.wi-env-seal.wi-seal-pal-gold-wax { --s-hi: rgba(255, 230, 140, 0.55); --s-mid: #b8860b; --s-lo: #6b4a08; --s-drip: #7a550a; --s-ink: rgba(255, 252, 235, 0.95); }
.wi-env-seal.wi-seal-pal-bronze { --s-hi: rgba(255, 200, 140, 0.4); --s-mid: #8b5a2b; --s-lo: #4a3018; --s-drip: #5c3a1c; --s-ink: rgba(255, 248, 240, 0.92); }
.wi-env-seal.wi-seal-pal-gold { --s-hi: rgba(255, 245, 180, 0.7); --s-mid: #c9a227; --s-lo: #7a5a10; --s-drip: #8a6512; --s-ink: #3d2810; }
.wi-env-seal.wi-seal-pal-silver { --s-hi: rgba(255, 255, 255, 0.75); --s-mid: #a8a8b0; --s-lo: #5a5a62; --s-drip: #6a6a72; --s-ink: #2a2a32; }
.wi-env-seal.wi-seal-pal-blush { --s-hi: rgba(255, 255, 255, 0.55); --s-mid: color-mix(in srgb, var(--wi-accent, #e8b4b8) 55%, #fff); --s-lo: #c8a0a8; --s-drip: transparent; --s-ink: color-mix(in srgb, var(--wi-gold) 85%, #3d2810); }
.wi-env-seal.wi-seal-pal-ivory { --s-hi: rgba(255, 255, 255, 0.9); --s-mid: #f5f0e6; --s-lo: #d8d0c0; --s-drip: transparent; --s-ink: #5a5048; }

/* Wax family */
.wi-env-seal[class*="wi-seal-shape-wax"] {
	color: var(--s-ink);
	text-shadow: 0 1px 2px rgba(0, 0, 0, 0.4), 0 -1px 0 rgba(255, 255, 255, 0.12);
	filter: drop-shadow(0 6px 14px rgba(0, 0, 0, 0.45));
}
.wi-env-seal[class*="wi-seal-shape-wax"]::before {
	content: '';
	position: absolute;
	inset: -4%;
	z-index: -1;
	background:
		radial-gradient(ellipse 35% 28% at 32% 28%, var(--s-hi), transparent 55%),
		radial-gradient(ellipse 80% 70% at 50% 55%, var(--s-mid), var(--s-lo) 100%);
	box-shadow: inset 0 3px 8px rgba(255, 255, 255, 0.2), inset 0 -6px 14px rgba(0, 0, 0, 0.32), 0 2px 4px rgba(0, 0, 0, 0.2);
}
.wi-env-seal.has-seal-drip[class*="wi-seal-shape-wax"]::after {
	content: '';
	position: absolute;
	width: 22%;
	height: 18%;
	left: 62%;
	top: 68%;
	border-radius: 50% 50% 45% 55%;
	background: linear-gradient(145deg, var(--s-mid), var(--s-drip));
	box-shadow: 0 3px 6px rgba(0, 0, 0, 0.35);
	transform: rotate(25deg);
	z-index: -2;
}
.wi-env-seal.wi-seal-shape-wax-round::before { border-radius: 47% 53% 52% 48% / 48% 45% 55% 52%; }
.wi-env-seal.wi-seal-shape-wax-oval::before { border-radius: 48% 52% 50% 50% / 42% 42% 58% 58%; }
.wi-env-seal.wi-seal-shape-wax-hex::before { clip-path: polygon(50% 0%, 93% 25%, 93% 75%, 50% 100%, 7% 75%, 7% 25%); border-radius: 0; }
.wi-env-seal.wi-seal-shape-wax-oct::before { clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%); border-radius: 0; }
.wi-env-seal.wi-seal-shape-wax-heart::before {
	clip-path: polygon(50% 92%, 6% 40%, 6% 26%, 20% 6%, 38% 6%, 50% 22%, 62% 6%, 80% 6%, 94% 26%, 94% 40%);
	border-radius: 0;
}
.wi-env-seal.wi-seal-shape-wax-shield::before { clip-path: polygon(50% 0%, 96% 18%, 96% 58%, 50% 100%, 4% 58%, 4% 18%); border-radius: 0; }
.wi-env-seal.wi-seal-shape-wax-square::before { border-radius: 14%; }
.wi-env-seal.wi-seal-shape-wax-star::before {
	clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
	border-radius: 0;
}
.wi-env-seal.wi-seal-shape-wax-double::before { border-radius: 50%; box-shadow: inset 0 0 0 5px rgba(255, 220, 200, 0.2), inset 0 3px 8px rgba(255, 255, 255, 0.2), inset 0 -6px 14px rgba(0, 0, 0, 0.32); }
.wi-env-seal.wi-seal-shape-wax-double .wi-seal-ring { inset: 18%; border-width: 3px; }

/* Floral emboss */
.wi-env-seal.wi-seal-shape-floral-round {
	text-shadow: 0 1px 0 rgba(255, 255, 255, 0.65), 0 -1px 1px rgba(0, 0, 0, 0.1);
	filter: drop-shadow(0 5px 12px rgba(0, 0, 0, 0.2));
	color: var(--s-ink);
}
.wi-env-seal.wi-seal-shape-floral-round::before {
	content: '';
	position: absolute;
	inset: 0;
	border-radius: 50%;
	background:
		radial-gradient(circle at 30% 25%, var(--s-hi), transparent 42%),
		radial-gradient(circle at 70% 75%, rgba(0, 0, 0, 0.06), transparent 45%),
		linear-gradient(145deg, var(--s-mid), color-mix(in srgb, var(--env-paper, #f5f0e6) 90%, #e8dcc8));
	box-shadow: inset 0 4px 10px rgba(255, 255, 255, 0.7), inset 0 -5px 12px rgba(120, 90, 60, 0.15), 0 0 0 3px color-mix(in srgb, var(--wi-gold) 70%, #8a7040), 0 0 0 5px color-mix(in srgb, var(--wi-gold) 22%, transparent);
	z-index: -1;
}
.wi-env-seal.wi-seal-shape-floral-round::after {
	content: '';
	position: absolute;
	inset: 8%;
	border-radius: 50%;
	background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' opacity='0.22'%3E%3Cg fill='none' stroke='%23a08050' stroke-width='0.6'%3E%3Ccircle cx='50' cy='50' r='18'/%3E%3Cpath d='M50 32 Q58 50 50 68 Q42 50 50 32'/%3E%3Cpath d='M32 50 Q50 58 68 50 Q50 42 32 50'/%3E%3C/g%3E%3C/svg%3E");
	background-size: cover;
	pointer-events: none;
	z-index: 0;
}

/* Gold / silver foil */
.wi-env-seal.wi-seal-shape-foil-round {
	font-weight: 800;
	color: var(--s-ink);
	text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
	filter: drop-shadow(0 5px 14px rgba(0, 0, 0, 0.35));
}
.wi-env-seal.wi-seal-shape-foil-round::before {
	content: '';
	position: absolute;
	inset: -2%;
	border-radius: 50%;
	background:
		radial-gradient(ellipse 40% 30% at 35% 28%, var(--s-hi), transparent 50%),
		conic-gradient(from 200deg at 50% 50%, var(--s-lo), var(--s-mid), var(--s-lo), var(--s-mid), var(--s-lo));
	box-shadow: inset 0 2px 6px rgba(255, 255, 255, 0.45), inset 0 -4px 10px rgba(0, 0, 0, 0.25), 0 0 0 2px color-mix(in srgb, var(--s-mid) 80%, #000);
	z-index: -1;
}
.wi-env-seal.wi-seal-pal-silver.wi-seal-shape-foil-round::before {
	background:
		radial-gradient(ellipse 40% 30% at 35% 28%, rgba(255,255,255,0.9), transparent 50%),
		linear-gradient(145deg, #d8d8e0, #909098 40%, #c0c0c8 60%, #787880);
}

/* Ivory paper emboss */
.wi-env-seal.wi-seal-shape-emboss-round {
	color: var(--s-ink);
	text-shadow: 0 1px 0 #fff, 0 -1px 1px rgba(0, 0, 0, 0.08);
	filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.12));
}
.wi-env-seal.wi-seal-shape-emboss-round::before {
	content: '';
	position: absolute;
	inset: 0;
	border-radius: 50%;
	background: radial-gradient(circle at 32% 28%, var(--s-hi), var(--s-mid) 55%, var(--s-lo));
	box-shadow: inset 0 3px 8px rgba(255, 255, 255, 0.9), inset 0 -4px 10px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.06);
	z-index: -1;
}

/* Royal crest shield */
.wi-env-seal.wi-seal-shape-royal-shield {
	color: var(--s-ink);
	font-weight: 800;
	filter: drop-shadow(0 6px 16px rgba(0, 0, 0, 0.4));
}
.wi-env-seal.wi-seal-shape-royal-shield::before {
	content: '';
	position: absolute;
	inset: -6% -4%;
	clip-path: polygon(50% 0%, 100% 12%, 100% 52%, 50% 100%, 0% 52%, 0% 12%);
	background:
		radial-gradient(ellipse 50% 40% at 50% 20%, var(--s-hi), transparent 55%),
		linear-gradient(180deg, var(--s-mid), var(--s-lo));
	box-shadow: inset 0 2px 8px rgba(255, 255, 255, 0.35), 0 0 0 3px color-mix(in srgb, var(--wi-gold) 75%, #000);
	z-index: -1;
}
.wi-env-seal.wi-seal-shape-royal-shield::after {
	content: '♔';
	position: absolute;
	top: 8%;
	left: 50%;
	transform: translateX(-50%);
	font-size: 0.55em;
	opacity: 0.35;
	z-index: 0;
	pointer-events: none;
}

.wi-env-seal-button {
	appearance: none;
	border: none;
	background: transparent;
	padding: 0;
	margin: 0;
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	font: inherit;
	color: inherit;
	letter-spacing: inherit;
}
