<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<footer class="sop-global-footer">
    <div class="sop-footer-container">
        <!-- Logo Row -->
        <div class="sop-footer-logo">
            <img src="<?php echo esc_url( SOP_URL . 'assets/images/logo_white.png' ); ?>" alt="IMPROVIA">
        </div>

        <!-- Columns Grid -->
        <div class="sop-footer-grid">
            <div class="sop-footer-col">
                <h3>POLICIES</h3>
                <ul>
                    <li><a href="#">POLÍTICAS DE PRIVACIDAD</a></li>
                    <li><a href="#">POLÍTICA DE COOKIES</a></li>
                    <li><a href="#">POLÍTICA DE SEGURIDAD</a></li>
                    <li><a href="#">AVISO LEGAL</a></li>
                </ul>
            </div>

            <div class="sop-footer-col">
                <h3>CONTACT</h3>
                <p>PLAÇA D'EGUILAZ 8 BIS 1º 1ª<br>08017 BARCELONA</p>
                <p><a href="mailto:IMPROVIACONTAC@GMAIL.COM">IMPROVIACONTAC@GMAIL.COM</a></p>
                <p><a href="mailto:IMPROVIACONTAC2@GMAIL.COM">IMPROVIACONTAC2@GMAIL.COM</a></p>
            </div>

            <div class="sop-footer-col">
                <h3>SERVICES</h3>
                <ul>
                    <li><a href="#">ATHLETES</a></li>
                    <li><a href="#">SPORTS SPECIALISTS</a></li>
                    <li><a href="#">COACHES</a></li>
                    <li><a href="#">IMPROVIA PRO</a></li>
                </ul>
            </div>

            <div class="sop-footer-col">
                <h3>MAS</h3>
                <ul>
                    <li><a href="#">BLOG</a></li>
                    <li><a href="#">PODCAST</a></li>
                    <li><a href="#">OTHER</a></li>
                </ul>
            </div>

            <div class="sop-footer-col">
                <h3>RRSS</h3>
                <div class="sop-social-icons">
                    <a href="#" class="sop-social-icon">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/youtube.png' ); ?>" alt="YouTube">
                    </a>
                    <a href="#" class="sop-social-icon">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/x.png' ); ?>" alt="X">
                    </a>
                    <a href="#" class="sop-social-icon">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/instagram.png' ); ?>" alt="Instagram">
                    </a>
                    <a href="#" class="sop-social-icon">
                        <img src="<?php echo esc_url( SOP_URL . 'assets/images/link.png' ); ?>" alt="LinkedIn">
                    </a>
                </div>
            </div>
        </div>

        <hr class="sop-footer-divider">

        <!-- Bottom Bar -->
        <div class="sop-footer-bottom">
            <div class="sop-copyright">
                &copy; IMPROVIA <?php echo date('Y'); ?>
            </div>
            <nav class="sop-footer-bottom-nav">
                <a href="#">POLÍTICAS DE PRIVACIDAD</a>
                <a href="#">POLÍTICA DE COOKIES</a>
                <a href="#">POLÍTICA DE SEGURIDAD</a>
                <a href="#">AVISO LEGAL</a>
            </nav>
        </div>
    </div>
</footer>
