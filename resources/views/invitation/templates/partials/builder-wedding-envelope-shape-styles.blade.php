{{-- Per-shape geometry for built + photo flap envelopes --}}
/* European: taller flap, deeper fold */

/* Modern square flap */
.wi-env-shape-square .wi-env-top-flap,
.wi-env-shape-square .wi-env-photo-flap {
height: 48%;
clip-path: polygon(0 0, 8% 78%, 50% 100%, 92% 78%, 100% 0);
border-radius: 2px 2px 0 0;
}
.wi-env-shape-square .wi-env-back { border-radius: 6px; }
.wi-env-shape-square .wi-env-bottom-flap {
clip-path: polygon(0 100%, 50% 8%, 100% 100%);
height: 42%;
}

/* Luxe: double crease, refined edges */
.wi-env-shape-luxe .wi-env-back {
box-shadow:
inset 0 1px 0 rgba(255, 255, 255, 0.55),
inset 0 0 0 1px color-mix(in srgb, var(--wi-gold) 22%, transparent);
}
.wi-env-shape-luxe .wi-env-top-flap,
.wi-env-shape-luxe .wi-env-photo-flap {
height: 52%;
clip-path: polygon(0 0, 6% 72%, 50% 96%, 94% 72%, 100% 0);
box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}
.wi-env-shape-luxe .wi-env-fold-line {
opacity: 0.85;
height: 2px;
background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--wi-gold) 50%, var(--env-paper-dark)),
transparent);
}
.wi-env-shape-luxe .wi-env-liner {
opacity: 1;
}

/* Vintage: soft worn corners */
.wi-env-shape-vintage .wi-env-back {
border-radius: 3px 5px 4px 6px;
}
.wi-env-shape-vintage .wi-env-top-flap,
.wi-env-shape-vintage .wi-env-photo-flap {
clip-path: polygon(2% 2%, 50% 98%, 98% 2%);
filter: sepia(0.08) contrast(1.02);
}
.wi-env-shape-vintage .wi-env-paper-texture { opacity: 0.55; mix-blend-mode: soft-light; }
.wi-env-shape-vintage .wi-env-pocket {
clip-path: polygon(0 2%, 50% 30%, 100% 2%, 100% 100%, 0 100%);
}

/* Side pocket: diagonal opening */
.wi-env-shape-pocket .wi-env-top-flap,
.wi-env-shape-pocket .wi-env-photo-flap {
height: 46%;
clip-path: polygon(0 0, 100% 0, 100% 100%, 0 55%);
transform-origin: 0% 0%;
}
.wi-env-envelope.wi-env-shape-pocket .wi-env-top-flap {
transform: rotateX(0deg) translateZ(1px);
}
.wi-envelope-gate.is-opening .wi-env-envelope.wi-env-shape-pocket .wi-env-top-flap,
.wi-envelope-gate.is-open .wi-env-envelope.wi-env-shape-pocket .wi-env-top-flap,
.wi-envelope-gate.is-opening .wi-env-envelope.wi-env-shape-pocket .wi-env-photo-flap,
.wi-envelope-gate.is-open .wi-env-envelope.wi-env-shape-pocket .wi-env-photo-flap {
transform: rotateX(-142deg) rotateZ(-4deg);
}
.wi-env-shape-pocket .wi-env-pocket {
clip-path: polygon(0 0, 100% 18%, 100% 100%, 0 100%);
height: 65%;
}
.wi-env-shape-pocket .wi-env-side.left {
clip-path: polygon(0 0, 100% 40%, 100% 100%, 0 100%);
width: 55%;
}
.wi-env-shape-pocket .wi-env-side.right {
clip-path: polygon(0 60%, 100% 0, 100% 100%, 0 100%);
width: 48%;
}
.wi-env-shape-pocket .wi-env-seal-wrap {
top: 48%;
transform: translate(-50%, -50%);
}
