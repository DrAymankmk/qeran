<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <title>{{__('admin.project-name')}} - {{$invitation->event_name}}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta
      content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"
      name="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"
    />
    <meta
      content="{{__('admin.project-name')}} - {{__('admin.Dashboard')}}"
      name="{{__('admin.project-name')}}"
    />
    <link
      rel="shortcut icon"
      href="{{asset('admin_assets/images/logo.png')}}"
    />
    <link rel="icon" href="{{asset('admin_assets/images/logo.png')}}" />
    <meta
      property="og:image"
      itemprop="image"
      content="{{asset('admin_assets/images/logo.png')}}"
    />
    <meta
      property="og:image:secure_url"
      itemprop="image"
      content="{{asset('admin_assets/images/logo.png')}}"
    />
    <meta property="og:image:width" content="400" />
    <meta property="og:image:height" content="300" />
    <meta property="og:image:type" content="image/png" />
    <meta property="og:description" content="{{$invitation->event_name}}" />

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js"></script>

    <style>
      @import "https://fonts.googleapis.com/css?family=Karla|Slackey|Sriracha|Cairo:300,400,600,700";

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        outline: none;
      }

      body {
        background: linear-gradient(
          135deg,
          #121223 0%,
          #1a1a3a 50%,
          #2d2d5f 100%
        );
        font-family: "Cairo", "Karla", sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        perspective: 1000px;
        position: relative;
      }

      body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(
            circle at 30% 40%,
            rgba(255, 255, 255, 0.05) 0%,
            transparent 50%
          ),
          radial-gradient(
            circle at 70% 60%,
            rgba(255, 255, 255, 0.03) 0%,
            transparent 50%
          );
        z-index: -1;
        pointer-events: none;
      }

      .container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        
        padding: 20px;
        overflow: visible;
        position: relative;
      }

      /* Glass effect */
      .glass {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(18, 18, 35, 0.6),
          inset 0 1px 0 rgba(255, 255, 255, 0.1);
      }

      /* Invitation States */
      .invitation-wrapper {
        position: relative;
        width: 600px;
        max-width: 90vw;
        min-height: 450px;
        overflow: visible;
      }

      /* Envelope */
      .envelope {
        background: linear-gradient(135deg, #2a2a4a, #3d3d6b, #4a4a7a);
        width: 100%;
        height: 369.2307692308px;
        position: relative;
        border-radius: 15px;
        overflow: visible;
        justify-content: center;
        align-items: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 4;
        box-shadow: 0 15px 50px rgba(18, 18, 35, 0.8),
          inset 0 1px 0 rgba(255, 255, 255, 0.1), 0 5px 15px rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 255, 255, 0.1);
      }

      .envelope:before,
      .envelope:after {
        content: "";
        position: absolute;
        bottom: 0;
      }

      .envelope:before {
        right: 0;
        border-bottom: 0px solid transparent;
        border-top: 369.2307692308px solid transparent;
        border-right: 600px solid #3d3d6b;
        border-radius: 0 15px 0 0;
        z-index: 2;
      }

      .envelope:after {
        left: 0;
        border-bottom: 0px solid transparent;
        border-top: 369.2307692308px solid transparent;
        border-left: 600px solid #4a4a7a;
        border-radius: 0 0 0 15px;
        z-index: 3;
      }

      .flap {
        border-right: 300px solid transparent;
        border-top: 184.6153846154px solid #4a4a7a;
        border-left: 300px solid transparent;
        position: absolute;
        left: 0;
        top: 0;
        z-index: 4;
        transform-origin: 50% 0%;
        border-radius: 15px 15px 0 0;
        filter: drop-shadow(0 5px 15px rgba(18, 18, 35, 0.5));
      }


  

      .mask {
        width: 95%;
          max-width: 580px;
          height: 1000px;
          overflow: visible; 
          justify-content: center;
          align-items: center;
          
      }

      .card {
        position: relative;
        width: 100%;
        height: 800px;
        margin: auto;
        transform-style: preserve-3d;
        transform-origin: 50% 100%;
        transform: translate(0, 120%) rotateY(0deg) scale(0.95);
        will-change: transform;
        z-index: 0;
        opacity: 0;
        visibility: hidden;
      }

      .face {
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        position: absolute;
        box-shadow: 0 8px 25px rgba(18, 18, 35, 0.4),
          0 4px 12px rgba(0, 0, 0, 0.3);
        border-radius: 15px;
        overflow: hidden;
      }

      /* .face:last-of-type {
        transform: translateZ(-3px) rotateY(180deg);
      } */

  

      .front {
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.95),
          rgba(255, 255, 255, 0.9)
        );
        backdrop-filter: blur(20px);
        display: flex;
        flex-direction: column;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        min-width: 100%;
        min-height: 800px;
      }

      .event-media-container {
        width: 100%;
        margin-bottom: 20px;
      }

      .front .event-image,
      .front .event-video {
        width: 100%;
        height: 280px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
        display: block;
      }

      .front .event-image:hover,
      .front .event-video:hover {
        transform: scale(1.02);
      }

      .front .event-video {
        background: #000;
      }

      .front .event-video::-webkit-media-controls {
        display: none !important;
      }

      .front .event-video::-webkit-media-controls-start-playback-button {
        display: none !important;
      }

      /* GIF and animated image support */
      .event-image[src$=".gif"],
      .success-event-image[src$=".gif"],
      .event-image[src$=".webp"],
      .success-event-image[src$=".webp"] {
        image-rendering: auto;
        image-rendering: crisp-edges;
        image-rendering: pixelated;
      }

      /* WebP and modern image format support */
      .event-image,
      .success-event-image {
        image-rendering: -webkit-optimize-contrast;
        image-rendering: optimize-contrast;
        image-rendering: crisp-edges;
        image-rendering: auto;
      }

      /* Video loading and error states */
      .event-video,
      .success-event-video {
        background-color: rgba(0, 0, 0, 0.8);
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolygon points='5,3 19,12 5,21'%3E%3C/polygon%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 60px 60px;
      }

      .event-video[poster],
      .success-event-video[poster] {
        background-image: none;
      }

      /* Ensure videos don't show loading indicators over content */
      .event-video:not([src]),
      .success-event-video:not([src]) {
        background-color: rgba(0, 0, 0, 0.2);
        background-image: none;
      }

      .front .event-name {
        font: normal 2.8em/1.2 "Cairo", sans-serif;
        font-weight: 700;
        color: #121223;
        text-align: center;
        margin-bottom: 25px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .front .event-description {
        color: #4a5568;
        text-align: center;
        margin-bottom: 35px;
        font-size: 1.3em;
        line-height: 1.7;
        font-weight: 500;
      }

      .response-buttons {
        display: flex;
        gap: 20px;
        margin-top: auto;
      }

      .btn {
        flex: 1;
        padding: 18px 30px;
        border: none;
        border-radius: 12px;
        font-size: 1.2em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
      }

      .btn-accept {
        background: linear-gradient(135deg, #4ade80, #22c55e);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
      }

      .btn-accept:hover {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5);
      }

      .btn-decline {
        background: transparent;
        color: #ef4444;
        border: 2px solid #ef4444;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
      }

      .btn-decline:hover {
        background: #ef4444;
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.4);
      }
      
      .btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .back {
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.95),
          rgba(255, 255, 255, 0.9)
        );
        backdrop-filter: blur(20px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        min-height: 800px;
      }

      .back img {
        width: 220px;
        height: 220px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease;
      }

      .back img:hover {
        transform: scale(1.05);
      }

      .back .qr-text {
        color: #121223;
        text-align: center;
        font-size: 1.4em;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }

      .open-button {
        font: normal 1.4em "Cairo", sans-serif;
        padding: 20px 50px;
        border-radius: 50px;
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.2),
          rgba(255, 255, 255, 0.1)
        );
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.4);
        position: absolute;
        left: 50%;
        top: 470px;
        transform: translate(-50%, -50%);
        z-index: 10;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(15px);
        font-weight: 700;
        animation: pulse 3s infinite;
        box-shadow: 0 8px 32px rgba(18, 18, 35, 0.6),
          inset 0 1px 0 rgba(255, 255, 255, 0.2);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      }

      .open-button:hover {
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.35),
          rgba(255, 255, 255, 0.25)
        );
        transform: translate(-50%, -50%) scale(1.08);
        box-shadow: 0 12px 40px rgba(18, 18, 35, 0.8),
          inset 0 1px 0 rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.6);
      }

      @keyframes pulse {
        0%,
        100% {
          transform: translate(-50%, -50%) scale(1);
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.6),
            0 0 0 0 rgba(255, 255, 255, 0.4);
        }
        50% {
          transform: translate(-50%, -50%) scale(1.05);
          box-shadow: 0 12px 40px rgba(18, 18, 35, 0.8),
            0 0 0 15px rgba(255, 255, 255, 0);
        }
      }

      /* Success/Decline Views */
      .status-view {
        display: none;
        max-width: 600px;
        min-width: 350px;
        margin: 0 auto;
        text-align: center;
        padding: 40px;
      }

      .status-view.active {
        display: block;
        animation: slideUp 0.8s ease-out;
      }

      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .status-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 2.5em;
      }

      .status-icon.success {
        background: linear-gradient(
          135deg,
          rgba(34, 197, 94, 0.2),
          rgba(34, 197, 94, 0.1)
        );
        color: #22c55e;
        border: 2px solid rgba(34, 197, 94, 0.3);
        box-shadow: 0 8px 32px rgba(34, 197, 94, 0.3);
      }

      .status-icon.declined {
        background: linear-gradient(
          135deg,
          rgba(239, 68, 68, 0.2),
          rgba(239, 68, 68, 0.1)
        );
        color: #ef4444;
        border: 2px solid rgba(239, 68, 68, 0.3);
        box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
      }

      .status-title {
        font-size: 2.5em;
        font-weight: 700;
        color: white;
        margin-bottom: 15px;
      }

      .status-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.2em;
        margin-bottom: 40px;
      }

      /* Success view image styles */
      .success-image-container {
        margin: 30px 0;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .success-event-image,
      .success-event-video {
        width: 100%;
        max-width: 400px;
        height: 200px;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3),
                    0 8px 25px rgba(18, 18, 35, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        display: block;
      }

      .success-event-image:hover,
      .success-event-video:hover {
        transform: scale(1.03);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4),
                    0 12px 35px rgba(18, 18, 35, 0.5),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
      }

      .success-event-video {
        background: rgba(0, 0, 0, 0.8);
      }

      .success-event-video::-webkit-media-controls-panel {
        background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
      }

      .success-event-video::-webkit-media-controls {
        border-radius: 0 0 20px 20px;
      }

      .event-title {
        font-size: 2.2em;
        font-weight: 700;
        color: white;
        margin-bottom: 15px;
        line-height: 1.2;
      }

      .event-host {
        color: rgba(255, 255, 255, 0.7);
        font-size: 1.1em;
        margin-bottom: 25px;
        font-weight: 500;
      }

      .event-description-status {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1em;
        line-height: 1.6;
        margin-bottom: 40px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
      }

      .event-details {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 30px;
      }

      .detail-item {
        padding: 20px;
        gap: 12px;
        margin-bottom: 15px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
      }

      .detail-item:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(18, 18, 35, 0.6),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
      }

      .detail-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
          135deg,
          rgba(255, 255, 255, 0.1) 0%,
          rgba(255, 255, 255, 0.05) 50%,
          rgba(255, 255, 255, 0.1) 100%
        );
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
      }

      .detail-item:hover::before {
        opacity: 1;
      }

      .detail-item {
        display: flex;
        align-items: center;
        gap: 16px;
      }

      .detail-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
        flex-shrink: 0;
      }

      .detail-icon svg {
        width: 24px;
        height: 24px;
        transition: transform 0.3s ease;
      }

      .detail-icon.date {
        color: #60a5fa;
        background: rgba(96, 165, 250, 0.1);
        border-color: rgba(96, 165, 250, 0.2);
      }

      .detail-icon.time {
        color: #4ade80;
        background: rgba(74, 222, 128, 0.1);
        border-color: rgba(74, 222, 128, 0.2);
      }

      .detail-icon.location {
        color: #f87171;
        background: rgba(248, 113, 113, 0.1);
        border-color: rgba(248, 113, 113, 0.2);
      }

      .detail-item:hover .detail-icon {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.15);
      }

      .detail-item:hover .detail-icon svg {
        transform: scale(1.1);
      }

      /* Location clickable styles */
      .location-clickable {
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .location-clickable:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        transform: translateY(-3px) !important;
        box-shadow: 0 15px 45px rgba(18, 18, 35, 0.7),
                    inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
      }

      .location-clickable .detail-icon.location {
        transition: all 0.3s ease;
      }

      .location-clickable:hover .detail-icon.location {
        background: rgba(248, 113, 113, 0.2) !important;
        border-color: rgba(248, 113, 113, 0.4) !important;
        transform: scale(1.1) !important;
      }

      .detail-content {
        flex: 1;
        min-width: 0;
      }

      .detail-label {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.85rem;
        margin-bottom: 6px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      }

      .detail-value {
        color: rgba(255, 255, 255, 0.95);
        font-weight: 600;
        font-size: 1.1rem;
        line-height: 1.4;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        word-break: break-word;
      }

      /* Force detail-item layout consistency across all mobile breakpoints */
      @media screen and (max-width: 1024px) {
        .detail-item {
          display: flex !important;
          align-items: center !important;
          flex-direction: row !important;
        }
      }

      .qr-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      }

      .qr-section img {
        width: 150px;
        height: 150px;
        margin: 0 auto 15px;
        display: block;
      }

      .qr-section p {
        color: #2d3748;
        font-size: 1em;
        font-weight: 500;
        margin: 0;
      }

      .flip-button {
        background: linear-gradient(
          135deg,
          rgba(18, 18, 35, 0.8),
          rgba(18, 18, 35, 0.6)
        );
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1em;
        font-weight: 600;
        margin-top: 20px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 15px rgba(18, 18, 35, 0.3);
      }

      .flip-button:hover {
        background: linear-gradient(
          135deg,
          rgba(18, 18, 35, 0.9),
          rgba(18, 18, 35, 0.7)
        );
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(18, 18, 35, 0.4);
      }

      .back-button {
        background: transparent;
        color: rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 12px 30px;
        border-radius: 25px;
        cursor: pointer;
        font-size: 1em;
        transition: all 0.3s ease;
      }

      .back-button:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
      }

     

      /* Hidden by default */
      .hidden {
        display: none !important;
      }

      /* ===== RESPONSIVE DESIGN ===== */
      
      /* Tablet Styles */
      @media screen and (max-width: 1024px) {
        .container {
          padding: 15px;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
        }
        
        .invitation-wrapper {
          width: 550px;
          max-width: 85vw;
        }
        
        .envelope {
          height: 338.4615384615px; /* Proportionally smaller */
        }
        
        .envelope:before {
          border-top: 338.4615384615px solid transparent;
          border-right: 550px solid #3d3d6b;
        }
        
        .envelope:after {
          border-top: 338.4615384615px solid transparent;
          border-left: 550px solid #4a4a7a;
        }
        
        .flap {
          border-right: 275px solid transparent;
          border-top: 169.2307692308px solid #4a4a7a;
          border-left: 275px solid transparent;
        }
        
        .mask {
          width: 95%;
          max-width: 580px;
          height: 1800px;
          /* justify-content: center;
          align-items: center;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center; */
        }
        
        .card {
          width: 100%;
          height: 800px;
        }
        
        .front {
          min-height: 800px;
          padding: 25px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 280px;
        }
        
        .front .event-name {
          font-size: 2.4em;
        }
        
        .front .event-description {
          font-size: 1.2em;
        }
        
        .btn {
          padding: 16px 25px;
          font-size: 1.1em;
        }
        
        .back {
          min-height: 800px;
          padding: 25px;
        }
        
        .back img {
          width: 200px;
          height: 200px;
        }
        
        .back .qr-text {
          font-size: 1.3em;
        }
        
        .open-button {
          top: 440px;
          font-size: 1.3em;
          padding: 18px 45px;
        }
        
        .status-view {
          padding: 35px;
          max-width: 600px;
          min-width: 350px;
        }
        
        .event-details {
          display: flex !important;
          flex-direction: column !important;
          gap: 14px;
        }
        
        .detail-item {
          padding: 14px;
          gap: 10px;
          background: rgba(255, 255, 255, 0.1) !important;
          backdrop-filter: blur(15px) !important;
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
          border-radius: 15px;
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                      inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }
        
        .detail-icon {
          width: 36px;
          height: 36px;
          border-radius: 10px;
        }
        
        .detail-icon svg {
          width: 20px;
          height: 20px;
        }
        
        .detail-label {
          font-size: 0.8rem;
        }
        
        .detail-value {
          font-size: 0.95rem;
        }
        
        .qr-section {
          padding: 25px;
        }
        
        .qr-section img {
          width: 130px;
          height: 130px;
        }

        .success-event-image,
        .success-event-video {
          max-width: 350px;
          height: 180px;
        }
      }
      
      /* Mobile Landscape */
      @media screen and (max-width: 768px) and (orientation: landscape) {
        .container {
          padding: 10px;
          height: 100%;
        }
        
        .invitation-wrapper {
          width: 450px;
          max-width: 80vw;
        }
        
        .envelope {
          height: 276.9230769231px;
        }
        
        .envelope:before {
          border-top: 276.9230769231px solid transparent;
          border-right: 450px solid #3d3d6b;
        }
        
        .envelope:after {
          border-top: 276.9230769231px solid transparent;
          border-left: 450px solid #4a4a7a;
        }
        
        .flap {
          border-right: 225px solid transparent;
          border-top: 138.4615384615px solid #4a4a7a;
          border-left: 225px solid transparent;
        }
        
        .mask {
          height: 1400px;
        }
        
        .card {
          width: 100%;
          height: 650px;
        }
        
        .front {
          min-height: 650px;
          padding: 20px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 220px;
        }
        
        .front .event-name {
          font-size: 2.2em;
        }
        
        .front .event-description {
          font-size: 1.1em;
        }
        
        .btn {
          padding: 14px 20px;
          font-size: 1.05em;
        }
        
        .back {
          min-height: 650px;
          padding: 20px;
        }
        
        .back img {
          width: 160px;
          height: 160px;
        }
        
        .back .qr-text {
          font-size: 1.2em;
        }
        
        .open-button {
          top: 360px;
        }
      }
      
      /* Mobile Portrait */
      @media screen and (max-width: 768px) {
        .container {
          padding: 10px;
          min-height: 100vh;
          align-items: flex;
          justify-content: center;
          align-items: center;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          padding-top: 5vh;
          height: 100%;

        }
        
        .invitation-wrapper {
          width: 400px;
          /* max-width: 95vw; */
          min-height: 400px;
          height: 100%;
        }
        
        .envelope {
          height: 246.1538461538px;
        }
        
        .envelope:before {
          border-top: 246.1538461538px solid transparent;
          border-right: 400px solid #3d3d6b;
        }
        
        .envelope:after {
          border-top: 246.1538461538px solid transparent;
          border-left: 400px solid #4a4a7a;
        }
        
        .flap {
          border-right: 200px solid transparent;
          border-top: 123.0769230769px solid #4a4a7a;
          border-left: 200px solid transparent;
        }
        
        .mask {
          width: 95%;
          /* max-width: 380px; */
          height: 1500px;
          /* justify-content: center;
          align-items: center;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center; */
        }
        
        .card {
          width: 100%;
          height: 700px;
        }
        
        .front {
          min-height: 700px;
          padding: 18px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 240px;
        }
        
        .front .event-name {
          font-size: 2em;
          margin-bottom: 15px;
        }
        
        .front .event-description {
          font-size: 1.1em;
          margin-bottom: 25px;
        }
        
        .response-buttons {
          gap: 15px;
          flex-direction: column;
        }
        
        .btn {
          padding: 16px 25px;
          font-size: 1.15em;
        }
        
        .back {
          min-height: 700px;
          padding: 18px;
        }
        
        .back img {
          width: 170px;
          height: 170px;
          margin-bottom: 20px;
        }
        
        .back .qr-text {
          font-size: 1.2em;
        }
        
        .open-button {
          font-size: 1.15em;
          padding: 16px 40px;
          top: 320px;
        }
        
        .status-view {
          padding: 20px 10px;
          min-width: 320px;
        }
        
        .status-title {
          font-size: 1.8em;
        }
        
        .status-subtitle {
          font-size: 1em;
        }
        
        .event-title {
          font-size: 1.6em;
          margin-bottom: 10px;
        }
        
        .event-host {
          font-size: 0.95em;
          margin-bottom: 18px;
        }
        
        .event-description-status {
          font-size: 0.9em;
          margin-bottom: 25px;
        }
        
        .status-icon {
          width: 50px;
          height: 50px;
          font-size: 1.8em;
        }
        
        .event-details {
          display: flex !important;
          flex-direction: column !important;
          gap: 15px;
          margin-bottom: 30px;
        }
        
        .detail-item {
          padding: 15px;
          gap: 12px;
          background: rgba(255, 255, 255, 0.1) !important;
          backdrop-filter: blur(15px) !important;
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
          border-radius: 15px;
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                      inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }
        
        .detail-icon {
          width: 38px;
          height: 38px;
          border-radius: 11px;
        }
        
        .detail-icon svg {
          width: 22px;
          height: 22px;
        }
        
        .detail-label {
          font-size: 0.8rem;
        }
        
        .detail-value {
          font-size: 1rem;
        }
        
        .qr-section {
          padding: 15px;
        }
        
        .qr-section img {
          width: 100px;
          height: 100px;
        }
        
        .qr-section p {
          font-size: 0.85em;
        }
        
        .flip-button,
        .back-button {
          padding: 12px 25px;
          font-size: 0.95em;
        }

        .success-event-image,
        .success-event-video {
          max-width: 300px;
          height: 160px;
        }
      }
      
      /* Small Mobile */
      @media screen and (max-width: 480px) {
        .container {
          padding: 8px;
          padding-top: 3vh;
        }
        
        .invitation-wrapper {
          width: 350px;
          max-width: 98vw;
          min-height: 350px;
        }
        
        .envelope {
          height: 215.3846153846px;
        }
        
        .envelope:before {
          border-top: 215.3846153846px solid transparent;
          border-right: 350px solid #3d3d6b;
        }
        
        .envelope:after {
          border-top: 215.3846153846px solid transparent;
          border-left: 350px solid #4a4a7a;
        }
        
        .flap {
          border-right: 175px solid transparent;
          border-top: 107.6923076923px solid #4a4a7a;
          border-left: 175px solid transparent;
        }
        
        .mask {
          max-width: 350px;
          height: 1300px;
       
        }
        
        .card {
          width: 100%;
          height: 600px;
        }
        
        .front {
          min-height: 600px;
          padding: 16px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 180px;
        }
        
        .front .event-name {
          font-size: 1.7em;
          margin-bottom: 10px;
        }
        
        .front .event-description {
          font-size: 1em;
          margin-bottom: 20px;
        }
        
        .btn {
          padding: 14px 20px;
          font-size: 1.05em;
        }
        
        .back {
          min-height: 600px;
          padding: 16px;
        }
        
        .back img {
          width: 140px;
          height: 140px;
          margin-bottom: 15px;
        }
        
        .back .qr-text {
          font-size: 1.1em;
        }
        
        .open-button {
          font-size: 1.05em;
          padding: 14px 35px;
          top: 280px;
        }
        
        .status-view {
          padding: 20px 10px;
          min-width: 300px;
        }
        
        .status-title {
          font-size: 1.8em;
        }
        
        .status-subtitle {
          font-size: 1em;
        }
        
        .event-title {
          font-size: 1.6em;
          margin-bottom: 10px;
        }
        
        .event-host {
          font-size: 0.95em;
          margin-bottom: 18px;
        }
        
        .event-description-status {
          font-size: 0.9em;
          margin-bottom: 25px;
        }
        
        .status-icon {
          width: 50px;
          height: 50px;
          font-size: 1.8em;
        }
        
        .event-details {
          display: flex !important;
          flex-direction: column !important;
          gap: 12px;
          margin-bottom: 25px;
        }
        
        .detail-item {
          padding: 12px;
          gap: 10px;
          background: rgba(255, 255, 255, 0.1) !important;
          backdrop-filter: blur(15px) !important;
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
          border-radius: 15px;
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                      inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }
        
        .detail-icon {
          width: 32px;
          height: 32px;
          border-radius: 8px;
        }
        
        .detail-icon svg {
          width: 18px;
          height: 18px;
        }
        
        .detail-label {
          font-size: 0.75rem;
        }
        
        .detail-value {
          font-size: 0.95rem;
        }
        
        .qr-section {
          padding: 15px;
        }
        
        .qr-section img {
          width: 100px;
          height: 100px;
        }
        
        .qr-section p {
          font-size: 0.85em;
        }
        
        .flip-button,
        .back-button {
          padding: 10px 20px;
          font-size: 0.9em;
        }

        .success-event-image,
        .success-event-video {
          max-width: 280px;
          height: 140px;
        }
      }
      
      /* Extra Small Mobile */
      @media screen and (max-width: 360px) {
        .invitation-wrapper {
          width: 320px;
        }
        
        .envelope {
          height: 196.9230769231px;
        }
        
        .envelope:before {
          border-top: 196.9230769231px solid transparent;
          border-right: 320px solid #3d3d6b;
        }
        
        .envelope:after {
          border-top: 196.9230769231px solid transparent;
          border-left: 320px solid #4a4a7a;
        }
        
        .flap {
          border-right: 160px solid transparent;
          border-top: 98.4615384615px solid #4a4a7a;
          border-left: 160px solid transparent;
        }
        
        .mask {
          max-width: 330px;
          height: 1300px;
        }
        
        .card {
          width: 100%;
          height: 550px;
        }
        
        .front {
          min-height: 550px;
          padding: 14px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 160px;
        }
        
        .front .event-name {
          font-size: 1.5em;
          margin-bottom: 8px;
        }
        
        .front .event-description {
          font-size: 0.95em;
          margin-bottom: 18px;
        }
        
        .btn {
          padding: 12px 18px;
          font-size: 1em;
        }
        
        .back {
          min-height: 550px;
          padding: 14px;
        }
        
        .back img {
          width: 120px;
          height: 120px;
          margin-bottom: 12px;
        }
        
        .back .qr-text {
          font-size: 1em;
        }
        
        .open-button {
          top: 260px;
          font-size: 1em;
          padding: 12px 30px;
        }
        
        .status-view {
          padding: 18px 8px;
          min-width: 280px;
        }
        
        .status-title {
          font-size: 1.6em;
        }
        
        .status-subtitle {
          font-size: 0.95em;
        }
        
        .event-title {
          font-size: 1.4em;
          margin-bottom: 8px;
        }
        
        .event-host {
          font-size: 0.9em;
          margin-bottom: 15px;
        }
        
        .event-description-status {
          font-size: 0.85em;
          margin-bottom: 20px;
        }
        
        .status-icon {
          width: 45px;
          height: 45px;
          font-size: 1.6em;
        }
        
        .event-details {
          display: flex !important;
          flex-direction: column !important;
          gap: 10px;
          margin-bottom: 20px;
        }
        
        .detail-item {
          padding: 10px;
          background: rgba(255, 255, 255, 0.1) !important;
          backdrop-filter: blur(15px) !important;
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
          border-radius: 15px;
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                      inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }
        
        .detail-icon {
          width: 30px;
          height: 30px;
          border-radius: 8px;
          margin-bottom: 0;
        }
        
        .detail-icon svg {
          width: 16px;
          height: 16px;
        }
        
        .detail-label {
          font-size: 0.7em;
        }
        
        .detail-value {
          font-size: 0.9em;
        }
        
        .qr-section {
          padding: 12px;
        }
        
        .qr-section img {
          width: 90px;
          height: 90px;
        }
        
        .qr-section p {
          font-size: 0.8em;
        }

        .success-event-image,
        .success-event-video {
          max-width: 250px;
          height: 120px;
        }
      }
      
      /* Landscape orientation adjustments for mobile */
      @media screen and (max-height: 550px) and (orientation: landscape) {
        .container {
          align-items: center;
          padding-top: 20px;
        }
        
        .invitation-wrapper {
          min-height: auto;
        }
        
        .mask {
          height: 1200px;
        }
        
        .card {
          height: 500px;
        }
        
        .front {
          min-height: 500px;
          padding: 12px;
        }
        
        .front .event-image,
        .front .event-video {
          height: 140px;
        }
        
        .front .event-name {
          font-size: 1.8em;
          margin-bottom: 8px;
        }
        
        .front .event-description {
          font-size: 1em;
          margin-bottom: 15px;
        }
        
        .btn {
          padding: 12px 20px;
          font-size: 1em;
        }
        
        .back {
          min-height: 500px;
          padding: 12px;
        }
        
        .back img {
          width: 120px;
          height: 120px;
          margin-bottom: 15px;
        }
        
        .back .qr-text {
          font-size: 1em;
        }
        
        .status-view {
          padding: 15px;
          min-width: 280px;
        }
        
        .status-title {
          font-size: 1.5em;
          margin-bottom: 8px;
        }
        
        .status-subtitle {
          margin-bottom: 15px;
          font-size: 1em;
        }
        
        .event-title {
          font-size: 1.4em;
          margin-bottom: 8px;
        }
        
        .event-host {
          font-size: 0.95em;
          margin-bottom: 12px;
        }
        
        .event-description-status {
          font-size: 0.9em;
          margin-bottom: 15px;
        }
        
        .status-icon {
          width: 50px;
          height: 50px;
          font-size: 1.8em;
          margin-bottom: 15px;
        }
        
        .event-details {
          display: flex !important;
          flex-direction: column !important;
          gap: 10px;
          margin-bottom: 20px;
        }
        
        .detail-item {
          padding: 10px;
          gap: 8px;
          background: rgba(255, 255, 255, 0.1) !important;
          backdrop-filter: blur(15px) !important;
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
          border-radius: 15px;
          box-shadow: 0 8px 32px rgba(18, 18, 35, 0.4),
                      inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;
        }
        
        .detail-icon {
          width: 32px;
          height: 32px;
          border-radius: 8px;
        }
        
        .detail-icon svg {
          width: 18px;
          height: 18px;
        }
        
        .detail-label {
          font-size: 0.75rem;
        }
        
        .detail-value {
          font-size: 0.9rem;
        }
        
        .qr-section {
          padding: 15px;
          margin-bottom: 15px;
        }
        
        .qr-section img {
          width: 80px;
          height: 80px;
        }
        
        .qr-section p {
          font-size: 0.85em;
        }

        .success-event-image,
        .success-event-video {
          max-width: 250px;
          height: 120px;
        }
      }

      /* User Information Section */
      .user-info-section {
        margin: 30px 0;
        padding: 30px;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(15px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      }

      .user-info-title {
        font-size: 24px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.95);
        text-align: center;
        margin-bottom: 25px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      }

      .user-info-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .user-info-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
      }

      .user-info-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        background: rgba(255, 255, 255, 0.15) !important;
      }

      .user-info-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
      }

      .user-info-icon:hover {
        transform: scale(1.05);
      }

      .user-icon {
        background: rgba(96, 165, 250, 0.2);
        color: #60a5fa;
      }

      .phone-icon {
        background: rgba(74, 222, 128, 0.2);
        color: #4ade80;
      }

      .count-icon {
        background: rgba(251, 191, 36, 0.2);
        color: #fbbf24;
      }

      .user-info-content-text {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 5px;
      }

      .user-info-label {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
      }

      .user-info-value {
        font-size: 18px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.95);
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
      }

      /* Mobile Responsive for User Info */
      @media screen and (max-width: 768px) {
        .user-info-section {
          margin: 20px 0;
          padding: 20px;
        }

        .user-info-title {
          font-size: 20px;
          margin-bottom: 20px;
        }

        .user-info-item {
          padding: 12px;
          gap: 12px;
        }

        .user-info-icon {
          width: 40px;
          height: 40px;
        }

        .user-info-value {
          font-size: 16px;
        }
      }

      @media screen and (max-width: 480px) {
        .user-info-section {
          margin: 15px 0;
          padding: 15px;
        }

        .user-info-title {
          font-size: 18px;
          margin-bottom: 15px;
        }

        .user-info-item {
          padding: 10px;
          gap: 10px;
        }

        .user-info-icon {
          width: 36px;
          height: 36px;
        }

        .user-info-value {
          font-size: 14px;
        }
      }
    </style>
  </head>

  <body>
    <div class="container">
      <!-- Envelope Invitation View -->
      <div id="envelopeView" class="invitation-wrapper">
        <div class="envelope">
          <div class="mask">
            <div class="card">
              <div class="face front">
                @if($invitation->image())
                <div class="event-media-container">
                  @php
                    $mediaUrl = $invitation->image();
                    $extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
                    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
                    $isVideo = in_array($extension, $videoExtensions);
                  @endphp
                  
                  @if($isVideo)
                    <a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
                      <video
                        class="event-image event-video"
                        autoplay
                        muted
                        loop
                        playsinline
                        preload="metadata"
                        onloadstart="this.style.backgroundImage='none'"
                        onerror="this.style.backgroundImage='none'; this.style.backgroundColor='rgba(0,0,0,0.3)'"
                      >
                        <source src="{{$mediaUrl}}" type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
                        <p>Your browser does not support the video tag.</p>
                      </video>
                    </a>
                  @else
                    <a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
                      <img
                        src="{{$mediaUrl}}"
                        alt="{{$invitation->event_name}}"
                        class="event-image"
                        loading="lazy"
                      />
                    </a>
                  @endif
                </div>
                @endif
                <h1 class="event-name">{{$invitation->event_name}}</h1>
                <div class="response-buttons">
                <button class="btn btn-primary high-button" onclick="openMediaInNewTab()">
                  اضغط هنا لعرض الدعوة    
                </button>
                </div>
                
                {{-- <p class="event-description">
                  {{$invitation->id}}
                </p> --}}

                <div class="response-buttons">
                    
                  <button class="btn btn-accept" onclick="acceptInvitation()">
                    ✓ قبول الدعوة
                  </button>
                  <button class="btn btn-decline" onclick="declineInvitation()">
                    ✗ رفض الدعوة
                  </button>
                </div>
              </div>
              <!-- <div class="face back">
                <img
                  src="{{$invitation->qr($invitation->id,$user->id)}}"
                  alt="رمز الاستجابة السريعة"
                />
                <p class="qr-text">اعرض هذا الرمز في الحدث</p>
                <button class="flip-button" onclick="toggleCard()">
                  العودة للدعوة
                </button>
              </div> -->
            </div>
          </div>
        </div>
        <div class="flap"></div>
        <button class="open-button" onclick="openEnvelope()">
          افتح الدعوة
        </button>
      </div>

      <!-- Success View -->
      <div id="successView" class="status-view glass">
        <div class="status-icon success">✓</div>
        <h2 class="status-title">تم قبول الدعوة!</h2>
        <p class="status-subtitle">
          شكراً لك على قبول الدعوة
        </p>

        @if($invitation->image())
        <div class="success-image-container">
          @php
            $mediaUrl = $invitation->image();
            $extension = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv', 'm4v', '3gp', 'wmv'];
            $isVideo = in_array($extension, $videoExtensions);
          @endphp
          
          @if($isVideo)
            <a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
              <video
                class="success-event-image success-event-video"
                controls
                muted
                loop
                playsinline
                preload="metadata"
                onloadstart="this.style.backgroundImage='none'"
                onerror="this.style.backgroundImage='none'; this.style.backgroundColor='rgba(0,0,0,0.3)'"
              >
                <source src="{{$mediaUrl}}" type="video/{{$extension === 'mov' ? 'quicktime' : $extension}}">
                <p>Your browser does not support the video tag.</p>
              </video>
            </a>
          @else
            <a href="{{$mediaUrl}}" target="_blank" rel="noopener noreferrer">
              <img
                src="{{$mediaUrl}}"
                alt="{{$invitation->event_name}}"
                class="success-event-image"
                loading="lazy"
              />
            </a>
          @endif
        </div>
        @endif

        <h3 class="event-title">{{$invitation->event_name}}</h3>
        <p class="event-host">{{$host_name}}</p>
        <p class="event-description-status">
          {{$category->getTranslation('ar')->description ?? ''}}
        </p>

        <div class="event-details">
          <div class="detail-item">
            <div class="detail-icon date">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19 3H18V1H16V3H8V1H6V3H5C3.89 3 3.01 3.9 3.01 5L3 19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V8H19V19ZM7 10H12V15H7V10Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="detail-content">
              <div class="detail-label">التاريخ</div>
              <div class="detail-value">
                {{$invitation->date}} 
              </div>
            </div>
          </div>
          <div class="detail-item">
            <div class="detail-icon time">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C6.48 2 2 6.48 2 12S6.48 22 12 22 22 17.52 22 12 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12S7.59 4 12 4 20 7.59 20 12 16.41 20 12 20ZM12.5 7H11V13L16.25 16.15L17 14.92L12.5 12.25V7Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="detail-content">
              <div class="detail-label">الوقت</div>
              <div class="detail-value">
                {{ \Carbon\Carbon::parse($invitation->time)->format('h:i A') }}
              </div>
            </div>
          </div>
          <div class="detail-item location-clickable" onclick="openLocation()">
            <div class="detail-icon location">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C8.13 2 5 5.13 5 9C5 14.25 12 22 12 22S19 14.25 19 9C19 5.13 15.87 2 12 2ZM12 11.5C10.62 11.5 9.5 10.38 9.5 9S10.62 6.5 12 6.5 14.5 7.62 14.5 9 13.38 11.5 12 11.5Z" fill="currentColor"/>
              </svg>
            </div>
            <div class="detail-content">
              <div class="detail-label">المكان</div>
              <div class="detail-value">
                {{$invitation->address}}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px; opacity: 0.7; vertical-align: middle;">
                  <path d="M14 3V5H17.59L7.76 14.83L9.17 16.24L19 6.41V10H21V3H14Z" fill="currentColor"/>
                  <path d="M19 19H5V5H12V3H5C3.89 3 3 3.9 3 5V19C3 20.1 3.89 21 5 21H19C20.1 21 21 20.1 21 19V12H19V19Z" fill="currentColor"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- User Information Section -->
        <div class="user-info-section">
          <h3 class="user-info-title">الضيف</h3>
          <div class="user-info-content">
            <div class="user-info-item">
              <div class="user-info-icon user-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" fill="currentColor"/>
                  <path d="M12 14C7.58172 14 4 17.5817 4 22H20C20 17.5817 16.4183 14 12 14Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="user-info-content-text">
                <div class="user-info-label">الاسم</div>
                <div class="user-info-value">
                  {{$user->pivot->name ?? $user->name ?? 'غير محدد'}}
                </div>
              </div>
            </div>

            <div class="user-info-item">
              <div class="user-info-icon phone-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M6.62 10.79C8.06 13.62 10.38 15.94 13.21 17.38L15.41 15.18C15.69 14.9 16.08 14.82 16.43 14.93C17.55 15.3 18.75 15.5 20 15.5C20.55 15.5 21 15.95 21 16.5V20C21 20.55 20.55 21 20 21C10.61 21 3 13.39 3 4C3 3.45 3.45 3 4 3H7.5C8.05 3 8.5 3.45 8.5 4C8.5 5.25 8.7 6.45 9.07 7.57C9.18 7.92 9.1 8.31 8.82 8.59L6.62 10.79Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="user-info-content-text">
                <div class="user-info-label">رقم الهاتف</div>
                <div class="user-info-value">
                  {{$user->phone ? ($user->country_code ?? '') . $user->phone : 'غير محدد'}}
                </div>
              </div>
            </div>

            <div class="user-info-item">
              <div class="user-info-icon count-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M16 4C18.2091 4 20 5.79086 20 8V16C20 18.2091 18.2091 20 16 20H8C5.79086 20 4 18.2091 4 16V8C4 5.79086 5.79086 4 8 4H16ZM16 2H8C4.68629 2 2 4.68629 2 8V16C2 19.3137 4.68629 22 8 22H16C19.3137 22 22 19.3137 22 16V8C22 4.68629 19.3137 2 16 2Z" fill="currentColor"/>
                  <path d="M7 12H17V10H7V12ZM10 16H17V14H10V16ZM7 8H17V6H7V8Z" fill="currentColor"/>
                </svg>
              </div>
              <div class="user-info-content-text">
                <div class="user-info-label">عدد الدعوات</div>
                <div class="user-info-value">
                  {{$user->pivot->invitation_count ?? 1}} دعوة
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="qr-section">
          <img
            src="{{$invitation->qr($invitation->id,$user->id)}}"
            alt="رمز الاستجابة السريعة"
          />
          <p>الرجاء الاحتفاظ بالرمز وابرازه لحارس القاعه</p>
        </div>
      </div>

      <!-- Decline View -->
      <div id="declineView" class="status-view glass">
        <div class="status-icon declined">✗</div>
        <h2 class="status-title">تم رفض الدعوة</h2>
        <p class="status-subtitle">
          نأسف لعدم قدرتك على الحضور إلى {{$invitation->event_name}}
        </p>
      </div>
    </div>

    <script>
      let isOpen = false;
      let isFlipped = false;
      let currentView = "envelope";

      function openEnvelope() {
        if (isOpen) return;

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
        
        // Stage 2: Open flap
        .to(".flap", 0.8, {
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
            const currentY = 100 - (progress * 300); // 120% to -40% = 160% range
            
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
        acceptBtn.innerHTML = '<span style="display:inline-block;width:20px;height:20px;border:2px solid #fff;border-radius:50%;border-top:2px solid transparent;animation:spin 1s linear infinite;"></span>';
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
        declineBtn.innerHTML = '<span style="display:inline-block;width:20px;height:20px;border:2px solid #ef4444;border-radius:50%;border-top:2px solid transparent;animation:spin 1s linear infinite;"></span>';
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
          
          // Stage 4: Close the flap
          .to(".flap", 0.6, {
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
            this.style.transform = (this.style.transform || '') + ' scale(0.95)';
          }, { passive: true });
          
          button.addEventListener('touchend', function(e) {
            this.style.transform = this.style.transform.replace(' scale(0.95)', '');
          }, { passive: true });
          
          button.addEventListener('touchcancel', function(e) {
            this.style.transform = this.style.transform.replace(' scale(0.95)', '');
          }, { passive: true });
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
              TweenMax.set(".invitation-wrapper", { y: newWrapperY });
            }
          }
        }, 300);
      }
      
      // Prevent zoom on double tap for iOS
      function preventDoubleTapZoom() {
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
          const now = (new Date()).getTime();
          if (now - lastTouchEnd <= 300) {
            event.preventDefault();
          }
          lastTouchEnd = now;
        }, false);
      }

      // Initialize
      document.addEventListener("DOMContentLoaded", function () {
        const initialView = "{{ $initialView }}";
        console.log("initialView", initialView);

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
        const latitude = {{ $invitation->latitude ?? 'null' }};
        const longitude = {{ $invitation->longitude ?? 'null' }};
        
        if (latitude && longitude) {
          // Check if user is on mobile
          const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
          
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
  </body>
</html>