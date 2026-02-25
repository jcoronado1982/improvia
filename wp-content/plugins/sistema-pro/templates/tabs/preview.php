<?php
/**
 * Template para la pestaña de Previsualización
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="sop-preview-container">
    <?php
    $current_user = wp_get_current_user();
    
    // Fetch custom data
    $nationality_id = get_user_meta( $current_user->ID, 'sop_nacionalidad_id', true );
    $edad = '28'; // Todo: calculate from sop_fecha_nacimiento if exists
    $club = 'Boca Juniors'; // Todo: fetch from appropriate taxonomy
    $perfil_desc = get_user_meta( $current_user->ID, 'sop_prof_description', true ) ?: 'Lorem ipsum dolor sit amet consectetur...';
    $pierna = 'Derecha';
    $peso = '62kg';
    $nivel = 'Junior';
    $altura = '1.72';
    $cirquix = '45cm';
    $pecho = '40cm';
    $brazos = '25cm';
    $pierna_size = '35cm';
    ?>

    <!-- Header + Subscription Layout -->
    <?php
    $is_provider = ( current_user_can( 'entrenador' ) || current_user_can( 'especialista' ) );
    ?>
    <div class="sop-preview-layout sop-preview-with-sidebar">
        
        <!-- Left Main Column -->
        <div class="sop-preview-main">
            
            <!-- Header Section -->
            <div class="sop-preview-header">
                <div class="sop-preview-image">
                    <img src="<?php echo esc_url( SOP_URL . 'assets/images/profile1.png' ); ?>" alt="Profile">
                </div>
                <div class="sop-preview-info">
                    <div class="sop-preview-rating">
                        <span class="sop-stars">★★★★★</span> <span class="sop-rating-count">(10)</span>
                    </div>
                    <h2 class="sop-preview-name"><?php echo esc_html( strtoupper($current_user->display_name) ); ?></h2>
                    <p class="sop-preview-group">Coach de futbol</p>
                    
                    <div class="sop-preview-tags">
                        <div class="sop-tag">Nivel <span class="sop-tag-gold"><?php echo esc_html( $nivel ); ?></span></div>
                        <div class="sop-tag">Nacionalidad <img src="<?php echo esc_url( SOP_URL . 'assets/images/flag_ar.png' ); ?>" alt="AR" style="width: 24px; vertical-align: middle; margin-left: 5px;"></div>
                    </div>
                    
                    <div class="sop-preview-tags">
                        <div class="sop-tag">Total jugadores <span>46</span></div>
                        <div class="sop-tag">Edad <span><?php echo esc_html( $edad ); ?></span></div>
                    </div>

                    <?php if ( $is_provider ) : ?>
                        <p class="sop-preview-result-time">⚡ Resultados en 2 dias</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( $is_provider ) : ?>
            <!-- Provider: Description below header -->
            <div class="sop-preview-card sop-full-width">
                <p class="sop-preview-text"><?php echo nl2br( esc_html( $perfil_desc ) ); ?></p>
            </div>

            <!-- Provider: BACKGROUND -->
            <div class="sop-preview-card sop-full-width">
                <h3 class="sop-preview-card-title"><?php esc_html_e( 'BACKGROUND', 'sistema-pro' ); ?></h3>
                <div class="sop-preview-bg-grid">
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Experiencia', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <li>5 años</li>
                            <li>Experiencia en fútbol y fútbol sala (AFC)</li>
                            <li>UEFA PRO / Capely / CHF</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Licencias', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <li>Licencia pro del entrenador capacitado</li>
                            <li>Continuo a través del entrenamiento</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Certificaciones', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <li>Certificado profesional técnico deportivo</li>
                            <li>Certificado profesional de preparación física</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Provider: POSICIONES ESPECIALIZADAS -->
            <div class="sop-preview-card sop-full-width">
                <h3 class="sop-preview-card-title"><?php esc_html_e( 'POSICIONES ESPECIALIZADAS', 'sistema-pro' ); ?></h3>
                <div class="sop-preview-positions-grid">
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Posiciones', 'sistema-pro' ); ?></h4>
                        <div class="sop-comp-tags">
                            <span class="sop-comp-tag">Portero</span>
                            <span class="sop-comp-tag">Lateral</span>
                            <span class="sop-comp-tag">Centrocap</span>
                            <span class="sop-comp-tag">Defensa Central</span>
                            <span class="sop-comp-tag">Interior completo</span>
                            <span class="sop-comp-tag">Mediocentro</span>
                            <span class="sop-comp-tag">Extremo</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Paises donde puedo entrenar', 'sistema-pro' ); ?></h4>
                        <div class="sop-comp-tags">
                            <span class="sop-comp-tag">Toda españa</span>
                            <span class="sop-comp-tag">Alianza de estrellas</span>
                            <span class="sop-comp-tag">Alianza de futbol</span>
                        </div>
                        <h4 class="sop-preview-bg-subtitle" style="margin-top: 20px;">Fases Defensivas</h4>
                        <div class="sop-comp-tags">
                            <span class="sop-comp-tag">Zona baja</span>
                            <span class="sop-comp-tag">Zona media baja</span>
                        </div>
                        <h4 class="sop-preview-bg-subtitle" style="margin-top: 20px;">Fases Ofensivas</h4>
                        <div class="sop-comp-tags">
                            <span class="sop-comp-tag">Construcción / Progresión</span>
                            <span class="sop-comp-tag">Dato de Nombre del Paso</span>
                            <span class="sop-comp-tag">Contraataque desorganizado</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php else : ?>
            <!-- Non-Provider: Original Sections -->
            <div class="sop-preview-content-grid">
                <div class="sop-preview-card">
                    <h3 class="sop-preview-card-title"><?php esc_html_e( 'QUIEN SOY', 'sistema-pro' ); ?></h3>
                    <p class="sop-preview-text"><?php echo nl2br( esc_html( $perfil_desc ) ); ?></p>
                </div>
                <div class="sop-preview-card">
                    <h3 class="sop-preview-card-title"><?php esc_html_e( 'MI COMPOSICION', 'sistema-pro' ); ?></h3>
                    <div class="sop-comp-tags">
                        <span class="sop-comp-tag">Pierna dominante / <?php echo esc_html( $pierna ); ?></span>
                        <span class="sop-comp-tag">Peso / <?php echo esc_html( $peso ); ?></span>
                        <span class="sop-comp-tag">Nivel / <?php echo esc_html( $nivel ); ?></span>
                        <span class="sop-comp-tag">Altura: <?php echo esc_html( $altura ); ?></span>
                        <span class="sop-comp-tag">Cirquix: <?php echo esc_html( $cirquix ); ?></span>
                        <span class="sop-comp-tag">Pecho: <?php echo esc_html( $pecho ); ?></span>
                        <span class="sop-comp-tag">Brazos: <?php echo esc_html( $brazos ); ?></span>
                        <span class="sop-comp-tag">Pierna: <?php echo esc_html( $pierna_size ); ?></span>
                    </div>
                </div>
            </div>

            <div class="sop-preview-card sop-full-width">
                <h3 class="sop-preview-card-title"><?php esc_html_e( 'REPORTE MEDICO / ESPECIALISTA', 'sistema-pro' ); ?></h3>
                <p class="sop-preview-text">Lorem ipsum dolor sit amet consectetur. Pretium at libero fermentum in vulputate. Viverra cum non ultricies tempor arcu in accumsan eu. Fringilla ut nulla neque leo phasellus tellus...</p>
            </div>
            <?php endif; ?>

            <!-- Social Media (Both roles) -->
            <?php 
            $rrss_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/components/rrss.php';
            if ( file_exists( $rrss_path ) ) {
                require $rrss_path;
            }
            ?>

            <!-- Reviews -->
            <?php 
            $reviews_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/components/reviews.php';
            if ( file_exists( $reviews_path ) ) {
                require $reviews_path;
            }
            ?>
            
        </div> <!-- End Left Main Column -->

        <!-- Right Sidebar (Subscription) -->
        <?php 
        $sidebar_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/components/subscription-sidebar.php';
        if ( file_exists( $sidebar_path ) ) {
            require $sidebar_path;
        }
        ?>

    </div>
</div>
