<?php

/**
 * @package     Bionic.Module.ImageSlider
 * @copyright   (C) 2025 Bionic Laboratories BLG GmbH
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

// DEBUG: Immer etwas anzeigen
echo '<!-- Bionic Image Slider Modul geladen - Bilder: ' . count($images) . ' -->';

// Falls keine Bilder, Hinweis anzeigen
if (empty($images)) {
    echo '<div style="padding:20px;background:#ffcc00;color:#000;border-radius:4px;margin:10px 0;">Bionic Slider: Keine Bilder konfiguriert</div>';
    return;
}

// Parameter auslesen
$width           = (int) $params->get('slider_width', 100) . '%';
$height          = $params->get('slider_height', '400px');
$interval        = (int) $params->get('slide_interval', 5000);
$transitionSpeed = (int) $params->get('transition_speed', 500);
$sliderPosition  = $params->get('slider_position', 'center');

$showNav         = (bool) $params->get('show_navigation', 1);
$showDots        = (bool) $params->get('show_dots', 1);
$autoplay        = (bool) $params->get('autoplay', 1);
$pauseOnHover    = (bool) $params->get('pause_on_hover', 1);
$showTitle       = (bool) $params->get('show_title', 1);
$titlePosition   = $params->get('title_position', 'bottom-left');

$borderRadius    = $params->get('border_radius', '0px');
$navColor        = $params->get('nav_color', '#ffffff');
$navBgColor      = $params->get('nav_bg_color', '#000000');
$dotColor        = $params->get('dot_color', '#cccccc');
$dotActiveColor  = $params->get('dot_active_color', '#0066cc');
$titleColor      = $params->get('title_color', '#ffffff');
$titleBgColor    = $params->get('title_bg_color', '#000000');
$titleBgOpacity  = (int) $params->get('title_bg_opacity', 70);

// Hex zu RGBA konvertieren
function modSliderHexToRgba($hex, $opacity = 1) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return "rgba({$r},{$g},{$b},{$opacity})";
}

// Titel-Position CSS
$titlePositions = [
    'top-left' => 'top:15px;left:15px;',
    'top-center' => 'top:15px;left:50%;transform:translateX(-50%);',
    'top-right' => 'top:15px;right:15px;',
    'bottom-left' => 'bottom:15px;left:15px;',
    'bottom-center' => 'bottom:15px;left:50%;transform:translateX(-50%);',
    'bottom-right' => 'bottom:15px;right:15px;',
];
$titlePosCSS = $titlePositions[$titlePosition] ?? $titlePositions['bottom-left'];

// Slider-Ausrichtung
$alignments = [
    'left' => 'margin:0 auto 20px 0;',
    'center' => 'margin:0 auto 20px auto;',
    'right' => 'margin:0 0 20px auto;',
];
$alignmentCSS = $alignments[$sliderPosition] ?? $alignments['center'];

// Farben
$titleBgRGBA = modSliderHexToRgba($titleBgColor, $titleBgOpacity / 100);
$navBgRGBA = modSliderHexToRgba($navBgColor, 0.5);
$navBgHover = modSliderHexToRgba($navBgColor, 0.8);

$slideCount = count($images);
$autoplayJs = $autoplay ? 'true' : 'false';
$pauseJs = $pauseOnHover ? 'true' : 'false';
?>

<style>
#<?php echo $sliderId; ?> {
    position: relative;
    width: <?php echo $width; ?>;
    max-width: 100%;
    <?php echo $alignmentCSS; ?>
    overflow: hidden;
    border-radius: <?php echo $borderRadius; ?>;
}
#<?php echo $sliderId; ?> .bionic-slider-wrapper {
    position: relative;
    width: 100%;
    height: <?php echo $height; ?>;
}
#<?php echo $sliderId; ?> .bionic-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity <?php echo $transitionSpeed; ?>ms ease;
    z-index: 1;
}
#<?php echo $sliderId; ?> .bionic-slide.active {
    opacity: 1;
    z-index: 2;
}
#<?php echo $sliderId; ?> .bionic-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
#<?php echo $sliderId; ?> .bionic-slide a {
    display: block;
    width: 100%;
    height: 100%;
}
#<?php echo $sliderId; ?> .bionic-slider-title {
    position: absolute;
    <?php echo $titlePosCSS; ?>
    padding: 12px 20px;
    background: <?php echo $titleBgRGBA; ?>;
    color: <?php echo $titleColor; ?>;
    font-size: 1.1em;
    z-index: 3;
    border-radius: 4px;
}
#<?php echo $sliderId; ?> .bionic-slider-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 10;
    background: <?php echo $navBgRGBA; ?>;
    color: <?php echo $navColor; ?>;
    border: none;
    padding: 15px 12px;
    cursor: pointer;
    font-size: 18px;
    transition: background 0.3s;
}
#<?php echo $sliderId; ?> .bionic-slider-nav:hover {
    background: <?php echo $navBgHover; ?>;
}
#<?php echo $sliderId; ?> .bionic-slider-prev {
    left: 10px;
}
#<?php echo $sliderId; ?> .bionic-slider-next {
    right: 10px;
}
#<?php echo $sliderId; ?> .bionic-slider-dots {
    position: absolute;
    bottom: 15px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
    display: flex;
    gap: 8px;
}
#<?php echo $sliderId; ?> .bionic-slider-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid <?php echo $dotActiveColor; ?>;
    background: <?php echo $dotColor; ?>;
    cursor: pointer;
    padding: 0;
}
#<?php echo $sliderId; ?> .bionic-slider-dot.active {
    background: <?php echo $dotActiveColor; ?>;
}
</style>

<div id="<?php echo $sliderId; ?>" class="bionic-image-slider">
    <div class="bionic-slider-wrapper">
        <?php foreach ($images as $index => $image): ?>
            <?php $activeClass = $index === 0 ? ' active' : ''; ?>
            <div class="bionic-slide<?php echo $activeClass; ?>">
                <?php if (!empty($image['link'])): ?>
                    <a href="<?php echo htmlspecialchars($image['link']); ?>">
                        <img src="<?php echo htmlspecialchars($image['src']); ?>" alt="<?php echo htmlspecialchars($image['alt']); ?>">
                    </a>
                <?php else: ?>
                    <img src="<?php echo htmlspecialchars($image['src']); ?>" alt="<?php echo htmlspecialchars($image['alt']); ?>">
                <?php endif; ?>
                <?php if ($showTitle && !empty($image['title'])): ?>
                    <div class="bionic-slider-title"><?php echo htmlspecialchars($image['title']); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($showNav && $slideCount > 1): ?>
        <button class="bionic-slider-nav bionic-slider-prev" aria-label="Vorheriges Bild">&#10094;</button>
        <button class="bionic-slider-nav bionic-slider-next" aria-label="Nächstes Bild">&#10095;</button>
    <?php endif; ?>

    <?php if ($showDots && $slideCount > 1): ?>
        <div class="bionic-slider-dots">
            <?php for ($i = 0; $i < $slideCount; $i++): ?>
                <?php $dotActive = $i === 0 ? ' active' : ''; ?>
                <button class="bionic-slider-dot<?php echo $dotActive; ?>" data-index="<?php echo $i; ?>" aria-label="Bild <?php echo $i + 1; ?>"></button>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<script>
(function(){
    var s = document.getElementById('<?php echo $sliderId; ?>');
    if (!s) return;
    
    var slides = s.querySelectorAll('.bionic-slide'),
        dots = s.querySelectorAll('.bionic-slider-dot'),
        prev = s.querySelector('.bionic-slider-prev'),
        next = s.querySelector('.bionic-slider-next'),
        cur = 0,
        cnt = <?php echo $slideCount; ?>,
        intv = <?php echo $interval; ?>,
        auto = <?php echo $autoplayJs; ?>,
        pause = <?php echo $pauseJs; ?>,
        timer = null;
    
    function show(i) {
        if (i >= cnt) i = 0;
        if (i < 0) i = cnt - 1;
        cur = i;
        slides.forEach(function(sl, j) {
            sl.classList.toggle('active', j === cur);
        });
        dots.forEach(function(d, j) {
            d.classList.toggle('active', j === cur);
        });
    }
    
    function nxt() { show(cur + 1); }
    function prv() { show(cur - 1); }
    function start() {
        if (auto && cnt > 1) {
            stop();
            timer = setInterval(nxt, intv);
        }
    }
    function stop() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }
    
    if (prev) prev.addEventListener('click', function() { prv(); start(); });
    if (next) next.addEventListener('click', function() { nxt(); start(); });
    
    dots.forEach(function(d) {
        d.addEventListener('click', function() {
            show(parseInt(this.dataset.index));
            start();
        });
    });
    
    if (pause) {
        s.addEventListener('mouseenter', stop);
        s.addEventListener('mouseleave', start);
    }
    
    start();
})();
</script>
