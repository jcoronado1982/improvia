<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<section class="hero-container">
    <video class="hero-video" autoplay loop muted playsinline>
        <source src="<?php echo esc_url( SOP_URL . 'assets/vidio/banner.mp4' ); ?>" type="video/mp4">
    </video>
    <div class="hero-overlay">
        <div class="sop-banner-title">THE WAY TO BE THE BEST, THE WAY TO IMPROVE</div>
        <p class="sop-banner-desc">La nueva manera de mejorar en el deporte, la plataforma para conseguir vivir de ello, la metodología del futuro ha llegado.</p>
        <div class="sop-banner-actions">
            <a href="<?php echo home_url('/registro?role=deportista'); ?>" class="sop-btn-outline-white">Join as athlete</a>
            <a href="<?php echo home_url('/registro?role=entrenador'); ?>" class="sop-btn-outline-white">Join as Coach</a>
        </div>
    </div>
</section>

    <!-- Features Section -->
    <div class="sop-landing-features">
        
        <!-- COACH Row -->
        <div class="sop-feature-row">
            <div class="sop-role-label">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/coach.png' ); ?>" alt="Coach">
            </div>
            <div class="sop-feature-items">
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h6.png' ); ?>" alt="Higher Profits">
                    <h4>HIGHER PROFITS</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h5.png' ); ?>" alt="Time Saving">
                    <h4>TIME SAVING</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h4.png' ); ?>" alt="Own Portfolio">
                    <h4>OWN PORTFOLIO</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
            </div>
        </div>

        <hr class="sop-feature-divider">

        <!-- FOOTBALLER Row -->
        <div class="sop-feature-row">
            <div class="sop-role-label">
                <img src="<?php echo esc_url( SOP_URL . 'assets/images/tootballer.png' ); ?>" alt="Footballer">
            </div>
            <div class="sop-feature-items">
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h3.png' ); ?>" alt="Personalized Tracking">
                    <h4>PERSONALIZED TRACKING</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h2.png' ); ?>" alt="Network of Coaches">
                    <h4>NETWORK OF COACHES</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
                <div class="sop-feature-item">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/h1.png' ); ?>" alt="Connection with other clubs">
                    <h4>CONNECTION WITH OTHER CLUBS</h4>
                    <p>Lorem ipsum dolor sit amet<br>consectetur. Augue vitae sed dolor</p>
                </div>
            </div>
        </div>
        
    </div>
</div>
