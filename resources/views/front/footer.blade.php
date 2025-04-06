<!-- Footer Start -->
<div class="container-fluid bg-dark text-light footer mt-5 pt-5">
  <div class="container py-5">
    <div class="row g-5">
      <div class="col-lg-3 col-md-6">
        <h5 class="text-light mb-4">Addresses</h5>
        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Karaçalı Mahallesi Şehit Hamza Atakul Caddesi No:39-A
          Dalaman/Muğla</p>
        <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Atatürk Mahallesi Postane Karşısı No:39-A
          Ortaca/Muğla</p>
        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>Dalaman: (530) 828 4884</p>
        <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>Ortaca: (546) 828 4884</p>
        <p class="mb-2"><i class="fa fa-envelope me-3"></i>aydindilakademisi@gmail.com</p>

        <div class="d-flex pt-2">
          <a class="btn btn-outline-light btn-social rounded-circle" href="#"><i class="fab fa-twitter"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="#"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="#"><i class="fab fa-youtube"></i></a>
          <a class="btn btn-outline-light btn-social rounded-circle" href="https://wa.me/905326666549?text=Selamlar"><i
              class="bi bi-whatsapp"></i></a>
        </div>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5 class="text-light mb-4">Quick Links</h5>
        <a class="btn btn-link" href="{{ Route('about') }}">About Us</a>
        <a class="btn btn-link" href="{{ Route('contact') }}">Contact Us</a>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="copyright">
      <div class="row">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
          <a class="border-bottom" href="{{ route('home') }}">AYDIN LANGUAGE ACADEMY</a>, All Right Reserved.
        </div>
        <div class="col-md-6 text-center text-md-end">
          <a class="border-none" href="{{ route('levels_list') }}">&copy; </a>
          Designed By <a class="border-bottom" href="http://www.madematik.com" target="_blank">mADEMatik</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Footer End -->


<!-- Back to Top -->
<a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-circle back-to-top"><i class="bi bi-arrow-up"></i></a>


<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('front/') }}/lib/waypoints/waypoints.min.js"></script>
<script src="{{ asset('front/') }}/lib/easing/easing.min.js"></script>
<script src="{{ asset('front/') }}/lib/wow/wow.min.js"></script>
<script src="{{ asset('front/') }}/lib/counterup/counterup.min.js"></script>
<script src="{{ asset('front/') }}/lib/owlcarousel/owl.carousel.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/moment.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/moment-timezone.min.js"></script>
<script src="{{ asset('front/') }}/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

<!-- Template Javascript -->
<script src="{{ asset('front/') }}/js/main.js"></script>
</body>

</html>