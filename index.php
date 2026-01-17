

<!DOCTYPE html>
<html lang="es">
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>Hydroponic</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- Menu css -->
      <link rel="stylesheet" href="./cultivos.css" />   <!-- OJOOOOO Seria conveniente quitar todo menos lo del menu -->
       
      <!-- bootstrap css -->
      <link rel="stylesheet" href="css/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" href="css/style.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="css/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
      <!-- Tweaks for older IEs-->
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" media="screen">
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
       

       
   </head>
    
   <!-- body -->
   <body class="main-layout">
      <!-- loader  -->
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#" /></div>
      </div>
      <!-- end loader -->
     
        <?php
            include ("cultivos_cabecera.inc");
        ?>  
        <br/>
        <br/>   
        <br/>
       
    <!-- banner --> 
      <section class="banner_main">
         <div class="container-fluid">
            <div class="row d_flex ">
               <div class="col-xl-4 col-lg-4 col-md-12">
                  <div class="banner_main_text">
                     <!--   <img class="logo" src="images/logo_cultivos.png" alt="#"/><!-- comentario -->
                     <div class="titlepage">
                        <h2>Interfaz web<br> del cultivo <br>Hidropónico & <br>Gestion</h2>
                        <p>Esta interfaz permite la completa gestión del cultivo hidropónico, acotando las tareas, llevando el control de las bandejas, operarios y especies, así como sus parámetros   </p>
                        <!--     <a class="read_more" href="#">estructura de la BDD</a>  --> <!-- comentario -->
                     </div>
                  </div>
               </div>
               <div class="col-xl-8 col-lg-8 col-md-12 padding_right">
                  <div class="banner_main_img">
                     <figure><img src="images/our_img1.jpg" alt="#"/>
                        <h3>HYDROPONIC</h3>
                     </figure>

                  </div>
               </div>
            </div>
         </div>
      </section>
      <!-- end banner -->
      <!-- about -->
      <div id="about"  class="about">
         <div class="container-fluid">
            <div class="row d_flex">
               <div class="col-xl-7 col-lg-7 col-md-12 padding_lert">
                  <div class="about_img">
                     <figure><img src="images/instalaciones.jpg" alt="#"/></figure>
                  </div>
               </div>
               <div class="col-xl-5 col-lg-5 col-md-12">
                  <div class="about_text">
                     <div class="titlepage">
                        <h2>Instalaciones</h2>
                        <p>Nuestras intalaciones son pioneras, situándose en un antiguo búnker remodelado de la segunda guerra mundial, completamente climatizadas, con la máxima seguridad, donde nuestros cultivos crecen con energía de origen 100% renovable </p>
                        <!--     <a class="read_more" href="#">Read More</a>  -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end about -->
      <!-- our -->
      <div id="our"  class="our">
         <div class="container-fluid">
            <div class="row d_flex">
               <div class="col-xl-5 col-lg-5 col-md-12 order_2">
                  <div class="our_text">
                     <div class="titlepage">
                        <h2>Nuestro  método</h2>
                        <p>Cumpliendo los más estrictos controles de calidad ISO, nuestro método permite que el control del ciclo de crecimiento de las plantas sea al milímetro, midiendo las concentraciones de nutrientes en cada bandeja individualmente, liberando lentamente los nutrientes en el agua, así como ajustando el flujo de agua, el PH y las horas de luz a cada especie, para garantizar la mayor producción dentro de nuestros compromisos de huella neutra de carbono y una explotación sostenible. </p>
                        <!--    <a class="read_more" href="#">Read More</a>  -->
                     </div>
                  </div>
               </div>
               <div class="col-xl-7 col-lg-7 col-md-12 padding_right order_1">
                  <div class="our_img">
                     <figure><img src="images/operario cosecha bandeja.jpg" alt="#"/></figure>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end our -->
      <!-- our_fruit -->
      <div id="our_fruit"  class="our_fruit">
         <div class="container-fluid">
            <div class="row d_flex">
               <div class="col-xl-7 col-lg-7 col-md-12 padding_lert">
                  <div class="row">
                     <div class="col-md-6 padd_lrri">
                        <div class="our_fruit_img">
                           <figure><img src="images/bandeja.jpg" alt="#"/></figure>
                        </div>
                     </div>
                     <div class="col-md-6 padd_lrri">
                        <div class="our_fruit_img">
                           <figure><img src="images/operario prepara bandeja.jpg" alt="#"/></figure>
                        </div>
                     </div>
                     <div class="col-md-6 padd_lrri">
                        <div class="our_fruit_img">
                           <figure><img src="images/operario comprueba cultivo.jpg" alt="#"/></figure>
                        </div>
                     </div>
                     <div class="col-md-6 padd_lrri">
                        <div class="our_fruit_img">
                           <figure><img src="images/operario mide porte.jpg" alt="#"/></figure>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-xl-5 col-lg-5 col-md-12">
                  <div class="our_fruit_text">
                     <div class="titlepage">
                        <h2>Personal</h2>
                        <p>Nuestro personal, altamente cualificado, es una parte muy importante de nuestra empresa, su constante formación y el buen ambiente de trabajo y colaboración nos permite asegurar un 99% de cumplimiento de las tareas dentro de plazo, así como una viabilidad del 100% en todas nuestras bandejas.</p>
                        <!--     <a class="read_more" href="#">Read More</a>  -->
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end our_fruit -->
      <!-- testimonial -->
      <div class="testimonial">
         <div class="container-fluid ">
            <div class="row d_flex ">
               <div class="col-xl-5 col-lg-5 col-md-12 order_2">
                  <div class="testimonial_box">
                     <div class="titlepage">
                        <h2>Testimonios</h2>
                     </div>
                     <p>Es sabido que la mejor forma de comprobar la calidad del servicio de una empresa es la satifación de sus clientes </p>
                  </div>
               </div>
               <div class="col-xl-7 col-lg-7 col-md-12 padding_right order_1">
                  <div id="myCarousel" class="carousel slide testimonial_Carousel " data-ride="carousel">
                     <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                     </ol>
                     <div class="carousel-inner">
                        <div class="carousel-item active">
                           <div class="container">
                              <div class="carousel-caption ">
                                 <div class="row">
                                    <div class="col-md-12 ">
                                       <div class="test_box">
                                          <h4>100 montaditos España</h4>
                                          <p>Nuestra relación comercial comenzó en 2012, dos años despúes de fundar nuestra franquicia, y como proveedores han sabido escalar junto a nosotros, siendo nuestro proveedor exclusivo de ensaladas frescas desde nuestros inicios con 20 franquiciados hasta ahora, con más de 350 franquicias en España
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="carousel-item">
                           <div class="container">
                              <div class="carousel-caption">
                                 <div class="row">
                                    <div class="col-md-12 ">
                                       <div class="test_box">
                                          <h4>Florette</h4>
                                          <p>Hydroponic nos provee desde hace 2 años productos para nuestras ensaladas preparadas y siempre se han mostrado competitivos y han superado sus objetivos, creciendo junto a nosostros como empresa
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="carousel-item">
                           <div class="container">
                              <div class="carousel-caption">
                                 <div class="row">
                                    <div class="col-md-12 ">
                                       <div class="test_box">
                                          <h4>Fresh food factory </h4>
                                          <p>Hydroponic is one of our best suppliers and it supllies fresh salad to our 152 restaurants in Europe, keeping our clients happy.
                                          </p>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
                     <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                     <span class="sr-only">Previous</span>
                     </a>
                     <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
                     <span class="carousel-control-next-icon" aria-hidden="true"></span>
                     <span class="sr-only">Next</span>
                     </a>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- end testimonial -->
      <!--  footer -->
      <footer>
         <div class="footer">
            <div class="container">
               <div class="row">
                  <div class="col-md-12">
                     <div class="cont">
                        <h3>Hydroponics</h3>
                        <p>Una solución sostenible y escalable para alimentar al mundo
                        </p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="copyright">
               <div class="container">
                  <div class="row">
                     <div class="col-md-12">
                        <p>Copyright 2022 All Right Reserved By Hydroponics Europe LTD</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </footer>
      <!-- end footer -->
      <!-- Javascript files-->
      <script src="js/jquery.min.js"></script>
      <script src="js/popper.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <script src="js/plugin.js"></script>
      <!-- sidebar -->
      <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
      <script src="js/custom.js"></script>
      <script src="https:cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
   </body>
</html>

