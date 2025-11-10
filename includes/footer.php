<?php
// includes/footer.php
?>
</main>
<style>
/* Footer styling using CSS variables */
footer.footer {
  background: #215dc6ff;
  color: #fff;
  padding: 15px 0;
  text-align: center;
  font-size: 14px;
  width: 100%;
  height: var(--footer-height);
  position: fixed;
  bottom: 0;
  left: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;
}

footer.footer .container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 0 20px;
  width: 100%;
}

/* Ensure body has bottom padding for fixed footer */
body {
  padding-bottom: var(--footer-height);
}
</style>
<footer class="footer">
  <div class="container">© <?= date('Y') ?> Quản lý Sinh viên &amp; Giảng viên</div>
</footer>
<script src="public/js/main.js"></script>
</body>
</html>
