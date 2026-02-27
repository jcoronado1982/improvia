<?php
/**
 * Template para la pestaña de Previsualización
 */
?>
<div class="sop-preview-container">
    <?php
    if ( isset( $sop_preview_user_id ) && $sop_preview_user_id ) {
        $current_user = get_userdata( $sop_preview_user_id );
    } else {
        $current_user = wp_get_current_user();
    }
    
    if ( ! $current_user || is_wp_error( $current_user ) ) {
        echo '<p>' . esc_html__( 'Usuario no encontrado.', 'sistema-pro' ) . '</p></div>';
        return;
    }
    
    // Fetch real data
    $nationality_id   = get_user_meta( $current_user->ID, 'sop_nacionalidad_id', true );
    $nationality_term = $nationality_id ? get_term( $nationality_id ) : null;
    $nationality_name = ( $nationality_term && ! is_wp_error( $nationality_term ) ) ? $nationality_term->name : '';
    $nationality_flag = SOP_UI::get_nationality_flag( $nationality_name );

    $perfil_desc = get_user_meta( $current_user->ID, 'sop_prof_description', true ) ?: __( 'Escribe aquí tu descripción profesional...', 'sistema-pro' );

    $categoria_id   = get_user_meta( $current_user->ID, 'sop_categoria_id', true );
    $categoria_term = $categoria_id ? get_term( $categoria_id ) : null;
    $categoria_name = ( $categoria_term && ! is_wp_error( $categoria_term ) ) ? $categoria_term->name : '';

    // Ocupación para el subtítulo
    $preview_ocup_id   = get_user_meta( $current_user->ID, 'sop_ocupacion_id', true );
    $preview_ocup_term = $preview_ocup_id ? get_term( $preview_ocup_id ) : null;
    $ocupacion_label   = ( $preview_ocup_term && ! is_wp_error( $preview_ocup_term ) ) ? $preview_ocup_term->name : '';

    // Nivel profesional
    $nivel_id   = get_user_meta( $current_user->ID, 'sop_nivel_prof_id', true );
    $nivel_term = $nivel_id ? get_term( $nivel_id ) : null;
    $nivel      = ( $nivel_term && ! is_wp_error( $nivel_term ) ) ? $nivel_term->name : '';

    // Edad (calculada desde fecha de nacimiento)
    $fecha_nac = get_user_meta( $current_user->ID, 'sop_fecha_nacimiento', true );
    $edad = '';
    if ( ! empty( $fecha_nac ) ) {
        try {
            $birth = new DateTime( $fecha_nac );
            $today = new DateTime();
            $edad  = $birth->diff( $today )->y;
        } catch ( Exception $ex ) {
            $edad = '';
        }
    }

    // Datos del atleta para preview
    $pierna_id   = get_user_meta( $current_user->ID, 'sop_pierna_id', true );
    $pierna_term = $pierna_id ? get_term( $pierna_id ) : null;
    $pierna      = ( $pierna_term && ! is_wp_error( $pierna_term ) ) ? $pierna_term->name : '';

    $peso_id   = get_user_meta( $current_user->ID, 'sop_peso_id', true );
    $peso_term = $peso_id ? get_term( $peso_id ) : null;
    $peso      = ( $peso_term && ! is_wp_error( $peso_term ) ) ? $peso_term->name . ' kg' : '';

    $altura_id   = get_user_meta( $current_user->ID, 'sop_altura_id', true );
    $altura_term = $altura_id ? get_term( $altura_id ) : null;
    $altura      = ( $altura_term && ! is_wp_error( $altura_term ) ) ? $altura_term->name . ' cm' : '';

    // Placeholders for composition (currently not in DB)
    $cirquix = $pecho = $brazos = $pierna_size = '';
    ?>

    <!-- Header + Subscription Layout -->
    <?php
    $is_provider = false;
    if ( $current_user && ! is_wp_error( $current_user ) ) {
        $roles = (array) $current_user->roles;
        if ( in_array( 'entrenador', $roles ) || in_array( 'especialista', $roles ) ) {
            $is_provider = true;
        }
    }
    ?>
    <div class="sop-preview-layout <?php echo $is_provider ? 'sop-preview-with-sidebar' : ''; ?>">
        
        <!-- Left Main Column -->
        <div class="sop-preview-main">
            
            <!-- Header Section -->
            <div class="sop-preview-header">
                <div class="sop-preview-image-card">
                    <?php
                    $profile_image_id = get_user_meta( $current_user->ID, 'sop_profile_image_id', true );
                    $profile_image_url = $profile_image_id ? wp_get_attachment_image_url( $profile_image_id, 'medium' ) : SOP_URL . 'assets/images/no image.png';
                    ?>
                    <img src="<?php echo esc_url( $profile_image_url ); ?>" alt="Profile" class="sop-preview-image-img">
                    <div class="sop-preview-play-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><polygon points="5,3 19,12 5,21"/></svg>
                    </div>
                </div>
                <div class="sop-preview-info">
                    <div class="sop-preview-rating">
                        <span class="sop-stars">★★★★★</span> <span class="sop-rating-count">(10)</span>
                    </div>
                    <h2 class="sop-preview-name"><?php echo esc_html( strtoupper($current_user->display_name) ); ?></h2>
                    <?php if ( ! empty( $ocupacion_label ) ) : ?>
                        <p class="sop-preview-group"><?php echo esc_html( $ocupacion_label ); ?></p>
                    <?php endif; ?>
                    
                    <div class="sop-preview-tags">
                        <?php if ( ! empty( $nivel ) ) : ?>
                            <div class="sop-tag"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?> <span class="sop-tag-gold"><?php echo esc_html( $nivel ); ?></span></div>
                        <?php endif; ?>
                        <?php if ( ! empty( $nationality_name ) ) : ?>
                            <div class="sop-tag"><?php esc_html_e( 'Nacionalidad', 'sistema-pro' ); ?> <?php if ($nationality_flag) : ?><img src="<?php echo esc_url($nationality_flag); ?>" alt="Flag" class="sop-flag-img"><?php endif; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="sop-preview-tags">
                        <?php if ( $is_provider ) : 
                            $all_txns = get_option( 'sop_mock_transactions_log', array() );
                            $total_jugadores = 0;
                            foreach ( $all_txns as $txn ) {
                                if ( isset($txn['trainer_id']) && intval($txn['trainer_id']) === $current_user->ID && ( $txn['status'] ?? '' ) === 'SUSCRIPCION_ACTIVA' ) {
                                    $total_jugadores++;
                                }
                            }
                        ?>
                            <div class="sop-tag"><?php esc_html_e( 'Total jugadores', 'sistema-pro' ); ?> <span><?php echo $total_jugadores; ?></span></div>
                        <?php endif; ?>

                        <?php if ( ! empty( $edad ) ) : ?>
                            <div class="sop-tag"><?php esc_html_e( 'Edad', 'sistema-pro' ); ?> <span><?php echo esc_html( $edad ); ?></span></div>
                        <?php endif; ?>
                    </div>

                    <?php if ( $is_provider ) : ?>
                        <p class="sop-preview-result-time"><img src="<?php echo esc_url( SOP_URL . 'assets/images/ray.png' ); ?>" alt="⚡" style="width:18px; height:18px; vertical-align:middle; margin-right:6px;"> <?php esc_html_e( 'Resultados en 2 dias', 'sistema-pro' ); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ( $is_provider ) :
            // Fetch coach professional data
            $ocupacion_id   = get_user_meta( $current_user->ID, 'sop_ocupacion_id', true );
            $experiencia_id = get_user_meta( $current_user->ID, 'sop_experiencia_id', true );
            $ocupacion_term   = $ocupacion_id ? get_term( $ocupacion_id ) : null;
            $experiencia_term = $experiencia_id ? get_term( $experiencia_id ) : null;

            $formacion_data = get_user_meta( $current_user->ID, 'sop_formacion_reglada_data', true ) ?: array();
            $estudios_data  = get_user_meta( $current_user->ID, 'sop_estudios_secundarios_data', true ) ?: array();

            $posiciones_ids       = get_user_meta( $current_user->ID, 'sop_posiciones_ids', true ) ?: array();
            $fases_ofensivas_ids  = get_user_meta( $current_user->ID, 'sop_fase_ofensiva_ids', true ) ?: array();
            $fases_defensivas_ids = get_user_meta( $current_user->ID, 'sop_fase_defensiva_ids', true ) ?: array();
            ?>
            <!-- Provider: Description below header -->
            <div class="sop-preview-card sop-full-width">
                <h3 class="sop-preview-card-title"><?php esc_html_e( 'DESCRIPCION PROFESIONAL', 'sistema-pro' ); ?></h3>
                <div class="sop-preview-text"><?php echo wp_kses_post( $perfil_desc ); ?></div>
            </div>

            <!-- Provider: BACKGROUND -->
            <div class="sop-preview-card sop-full-width">
                <h3 class="sop-preview-card-title"><?php esc_html_e( 'BACKGROUND', 'sistema-pro' ); ?></h3>
                <div class="sop-preview-bg-grid">
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Experiencia', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <?php if ( $ocupacion_term && ! is_wp_error( $ocupacion_term ) ) : ?>
                                <li><?php echo esc_html( $ocupacion_term->name ); ?></li>
                            <?php endif; ?>
                            <?php if ( $experiencia_term && ! is_wp_error( $experiencia_term ) ) : ?>
                                <li><?php echo esc_html( $experiencia_term->name ); ?></li>
                            <?php endif; ?>
                            <?php if ( empty( $ocupacion_term ) && empty( $experiencia_term ) ) : ?>
                                <li class="sop-empty-state"><?php esc_html_e( 'Sin datos registrados', 'sistema-pro' ); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Formación Reglada', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <?php if ( ! empty( $formacion_data ) ) : ?>
                                <?php foreach ( $formacion_data as $f ) : ?>
                                    <li><?php echo esc_html( ( $f['cert_name'] ?? '' ) . ' — ' . ( $f['instituto_name'] ?? '' ) ); ?></li>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <li class="sop-empty-state"><?php esc_html_e( 'Sin datos registrados', 'sistema-pro' ); ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Certificaciones', 'sistema-pro' ); ?></h4>
                        <ul class="sop-preview-bg-list">
                            <?php if ( ! empty( $estudios_data ) ) : ?>
                                <?php foreach ( $estudios_data as $e ) : ?>
                                    <li><?php echo esc_html( ( $e['cert_name'] ?? '' ) . ' — ' . ( $e['instituto_name'] ?? '' ) ); ?></li>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <li class="sop-empty-state"><?php esc_html_e( 'Sin datos registrados', 'sistema-pro' ); ?></li>
                            <?php endif; ?>
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
                            <?php if ( ! empty( $posiciones_ids ) ) : ?>
                                <?php foreach ( $posiciones_ids as $pid ) :
                                    $term = get_term( $pid );
                                    if ( $term && ! is_wp_error( $term ) ) : ?>
                                        <span class="sop-comp-tag"><?php echo esc_html( $term->name ); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span class="sop-comp-tag sop-empty-state"><?php esc_html_e( 'Sin posiciones seleccionadas', 'sistema-pro' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div>
                        <h4 class="sop-preview-bg-subtitle"><?php esc_html_e( 'Fase Ofensiva', 'sistema-pro' ); ?></h4>
                        <div class="sop-comp-tags">
                            <?php if ( ! empty( $fases_ofensivas_ids ) ) : ?>
                                <?php foreach ( $fases_ofensivas_ids as $foid ) :
                                    $term = get_term( $foid );
                                    if ( $term && ! is_wp_error( $term ) ) : ?>
                                        <span class="sop-comp-tag"><?php echo esc_html( $term->name ); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span class="sop-comp-tag sop-empty-state"><?php esc_html_e( 'Sin fases seleccionadas', 'sistema-pro' ); ?></span>
                            <?php endif; ?>
                        </div>

                        <h4 class="sop-preview-bg-subtitle" style="margin-top: 20px;"><?php esc_html_e( 'Fase Defensiva', 'sistema-pro' ); ?></h4>
                        <div class="sop-comp-tags">
                            <?php if ( ! empty( $fases_defensivas_ids ) ) : ?>
                                <?php foreach ( $fases_defensivas_ids as $fdid ) :
                                    $term = get_term( $fdid );
                                    if ( $term && ! is_wp_error( $term ) ) : ?>
                                        <span class="sop-comp-tag"><?php echo esc_html( $term->name ); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span class="sop-comp-tag sop-empty-state"><?php esc_html_e( 'Sin fases seleccionadas', 'sistema-pro' ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php else : ?>
            <!-- Non-Provider: Original Sections -->
            <div class="sop-preview-content-grid">
                <div class="sop-preview-card">
                    <h3 class="sop-preview-card-title"><?php esc_html_e( 'QUIEN SOY', 'sistema-pro' ); ?></h3>
                    <div class="sop-preview-text"><?php echo wp_kses_post( $perfil_desc ); ?></div>
                </div>
                <div class="sop-preview-card">
                    <h3 class="sop-preview-card-title"><?php esc_html_e( 'MI COMPOSICION', 'sistema-pro' ); ?></h3>
                    <div class="sop-comp-tags">
                        <?php if ( ! empty( $pierna ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Pierna dominante', 'sistema-pro' ); ?> / <?php echo esc_html( $pierna ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $peso ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Peso', 'sistema-pro' ); ?> / <?php echo esc_html( $peso ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $nivel ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Nivel', 'sistema-pro' ); ?> / <?php echo esc_html( $nivel ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $altura ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Altura', 'sistema-pro' ); ?> / <?php echo esc_html( $altura ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $cirquix ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Cirquix', 'sistema-pro' ); ?>: <?php echo esc_html( $cirquix ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $pecho ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Pecho', 'sistema-pro' ); ?>: <?php echo esc_html( $pecho ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $brazos ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Brazos', 'sistema-pro' ); ?>: <?php echo esc_html( $brazos ); ?></span>
                        <?php endif; ?>
                        <?php if ( ! empty( $pierna_size ) ) : ?>
                            <span class="sop-comp-tag"><?php esc_html_e( 'Pierna', 'sistema-pro' ); ?>: <?php echo esc_html( $pierna_size ); ?></span>
                        <?php endif; ?>
                        <?php if ( empty( $pierna ) && empty( $peso ) && empty( $nivel ) && empty( $altura ) ) : ?>
                            <span class="sop-comp-tag sop-empty-state"><?php esc_html_e( 'Sin datos registrados', 'sistema-pro' ); ?></span>
                        <?php endif; ?>
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
        if ( $is_provider ) {
            $sidebar_path = plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'templates/components/subscription-sidebar.php';
            if ( file_exists( $sidebar_path ) ) {
                require $sidebar_path;
            }
        }
        ?>

    </div>
</div>
