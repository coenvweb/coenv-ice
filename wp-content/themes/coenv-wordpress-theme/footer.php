
                <footer id="footer" role="contentinfo" class="site-footer">

                    <div class="layout-container">

                        <header class="site-footer__header">
                            <h1><span>University of Washington College of the Environment</span></h1>
                        </header>

                        <div class="footer__info">
                            <?php get_search_form() ?>
                            <?php wp_nav_menu(array(
                                'theme_location' => 'top-links', 
                                'depth' => 1,
                                'menu_class' => 'top-links',
                                'container' => false, 
                                'walker' => new CoEnv_Top_Menu_Walker(),
                                'fallback_cb' => false
                            )); ?>
                        </div>

                        <nav class="footer-nav">
                            <!--<h1 class="footer-nav__title">Units and programs</h1>-->
                            <?php wp_nav_menu( array(
                                'theme_location' => 'footer-units',
                                'depth' => 1,
                                'menu_class' => 'menu-footer-units',
                                'container' => false,
                                'fallback_cb' => false
                            ) ) ?>
                        </nav>

                        <div class="uw-footer">
                            <p class="copyright">&copy; <?php echo date('Y') ?> <a href="http://www.washington.edu/">University of Washington</a></p>

                            <?php wp_nav_menu( array(
                                'theme_location' => 'footer-links',
                                'depth' => 1,
                                'menu_class' => 'menu-footer-links',
                                'container' => false,
                                'fallback_cb' => false
                            ) ) ?>
                        </div>

                    </div><!-- .container -->

                </footer><!-- #footer -->

            </div><!-- #wrapper -->

        </div><!-- #outer -->

        <!--[if lt IE 8]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
        
        <?php wp_footer() ?>

        <!--
        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
        -->
    </body>
</html>