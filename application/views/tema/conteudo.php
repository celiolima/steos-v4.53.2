<div id="content">
<!--start-top-serch-->
  <div id="content-header">
   <div></div>
      <div id="breadcrumb">
        <a href="<?= base_url() ?>" title="Dashboard" class="tip-bottom"> Início</a>
        <?php if ($this->uri->segment(1) != null) { ?>
            <a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) ?>" class="tip-bottom" title="<?= ucfirst($this->uri->segment(1)); ?>">
              <?= ucfirst($this->uri->segment(1)); ?>
            </a>
          <?php if ($this->uri->segment(2) != null) { ?>
            <a href="<?= base_url() . 'index.php/' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . $this->uri->segment(3) ?>" class="current tip-bottom" title="<?= ucfirst($this->uri->segment(2)); ?>">
              <?= ucfirst($this->uri->segment(2));
          } ?>
            </a>
          <?php } ?>
      </div>
    </div>
    <div class="container-flu">
      <div class="row-fluid">
        <div class="span12">
          <?php if ($var = $this->session->flashdata('success')): ?>
              <script>
                  var rawMsg = <?php echo json_encode($var); ?> || "";
                  var cleanText = rawMsg.replace(/<br\s*[\/]?>/gi, "\n").replace(/\r\n/g, "\n").replace(/\n+/g, "\n").trim();
                  if (typeof Swal !== 'undefined') {
                      Swal.fire({ title: "Sucesso!", html: cleanText.replace(/\n/g, "<br>"), icon: "success" });
                  } else {
                      swal("Sucesso!", cleanText, "success");
                  }
              </script>
          <?php endif; ?>
          <?php if ($var = $this->session->flashdata('error')): ?>
              <script>
                  var rawMsg = <?php echo json_encode($var); ?> || "";
                  var cleanText = rawMsg.replace(/<br\s*[\/]?>/gi, "\n").replace(/\r\n/g, "\n").replace(/\n+/g, "\n").trim();
                  if (typeof Swal !== 'undefined') {
                      Swal.fire({ title: "Falha!", html: cleanText.replace(/\n/g, "<br>"), icon: "error" });
                  } else {
                      swal("Falha!", cleanText, "error");
                  }
              </script>
          <?php endif; ?>
          <?php if (isset($view)) {
              echo $this->load->view($view, null, true);
          } ?>
        </div>
      </div>
    </div>
  </div>
