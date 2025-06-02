<style>
  body {
    font-family: 'Arial', sans-serif;
    font-size: 13px;
    color: #111;
    margin: 0;
    padding: 40px;
    background-color: #fff;
  }

  .facture {
    border: 1px solid #ddd;
    padding: 30px;
    border-radius: 8px;
    max-width: 800px;
    margin: auto;
  }

  .facture-title {
    font-size: 24px;
    font-weight: bold;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-bottom: 1px solid #ccc;
    padding-bottom: 10px;
    margin-bottom: 30px;
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
  }

  .logo-container img {
    height: 60px;
  }

  .entreprise-nom {
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
  }

  .facture-infos {
    font-size: 14px;
    margin-bottom: 25px;
    line-height: 1.5;
  }

  .facture-client {
    font-size: 14px;
    margin-bottom: 20px;
    background-color: #f9f9f9;
    padding: 12px 15px;
    border-left: 4px solid #d90429;
    line-height: 1.6;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  th, td {
    border: 1px solid #ccc;
    padding: 10px;
    vertical-align: middle;
    font-size: 13px;
  }

  th {
    background-color: #f5f5f5;
    text-align: left;
  }

  .product-img {
    height: auto;
    max-height: 80px;
    width: auto;
    max-width: 100px;
    display: block;
    object-fit: contain;
  }

  .facture-total {
    font-weight: bold;
    background-color: #f0f0f0;
  }

  .text-right {
    text-align: right;
  }

  .footer-thanks {
    margin-top: 30px;
    font-style: italic;
    text-align: center;
    font-size: 12px;
    color: #666;
  }
</style>

<div class="facture">
  <div class="facture-title">Facture</div>

  <div class="logo-container">
    <img src="../public/img/logo.png" alt="Logo">
    <div class="entreprise-nom">Persona World</div>
  </div>

  <div class="facture-infos">
    <p><strong>Facture n°</strong> <?= $commandeData['idCommande'] ?></p>
    <p><strong>Date :</strong> <?= date('d/m/Y à H:i', strtotime($commandeData['dateCommande'])) ?></p>
  </div>

  <div class="facture-client">
    <p><strong>Client :</strong> <?= ucfirst($commandeData['clientPrenom']) . ' ' . strtoupper($commandeData['clientNom']) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($_SESSION['login']) ?></p>
  </div>

  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>Produit</th>
        <th>Prix unitaire</th>
        <th>Quantité</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php $total = 0; foreach ($produits as $ligne):
        $ligneTotal = $ligne['prix'] * $ligne['quantite'];
        $total += $ligneTotal;
      ?>
      <tr>
        <td>
          <?php if (!empty($ligne['img'])): ?>
            <img src="<?= $ligne['img'] ?>" class="product-img">
          <?php else: ?>
            <span style="color:#888;">N/A</span>
          <?php endif; ?>
        </td>
        <td><?= htmlspecialchars($ligne['produitNom']) ?></td>
        <td><?= number_format($ligne['prix'], 2) ?> €</td>
        <td><?= $ligne['quantite'] ?></td>
        <td><?= number_format($ligneTotal, 2) ?> €</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr class="facture-total">
        <td colspan="4" class="text-right">Total</td>
        <td><?= number_format($total, 2) ?> €</td>
      </tr>
    </tfoot>
  </table>

  <div class="footer-thanks">Merci pour votre confiance et à bientôt sur Persona World !</div>
</div>
