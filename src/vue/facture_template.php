<style>
  .facture {
    font-family: Arial, sans-serif;
    font-size: 13px;
    padding: 30px;
    color: #111;
  }

  .facture-header {
    text-align: center;
    margin-bottom: 20px;
  }

  .facture-title {
    font-size: 24px;
    margin-bottom: 5px;
  }

  .facture-date {
    font-size: 14px;
    color: #444;
  }

  .facture-client {
    font-size: 14px;
    margin: 10px 0 20px;
  }

  .facture-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }

  .facture-table th,
  .facture-table td {
    border: 1px solid #aaa;
    padding: 8px;
    text-align: left;
    font-size: 13px;
  }

  .facture-table th {
    background-color: #f0f0f0;
  }

  .facture-total td {
    font-weight: bold;
    background-color: #f9f9f9;
  }
</style>

<div class="facture">
  <div class="facture-header">
    <h1 class="facture-title">Facture n°<?= $details[0]['id'] ?></h1>
    <p class="facture-date"><strong>Date :</strong> <?= $details[0]['dateCommande'] ?></p>
  </div>

  <div class="facture-client">
    <p><strong>Client :</strong> <?= ucfirst($details[0]['prenom']) . ' ' . strtoupper($details[0]['nom']) ?></p>
  </div>

  <table class="facture-table">
    <thead>
      <tr>
        <th>Produit</th>
        <th>Prix unitaire</th>
        <th>Quantité</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php $total = 0; foreach ($details as $ligne): 
        $ligneTotal = $ligne['prix'] * $ligne['quantite'];
        $total += $ligneTotal;
      ?>
      <tr>
        <td><?= htmlspecialchars($ligne['produitNom']) ?></td>
        <td><?= number_format($ligne['prix'], 2) ?> €</td>
        <td><?= $ligne['quantite'] ?></td>
        <td><?= number_format($ligneTotal, 2) ?> €</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr class="facture-total">
        <td colspan="3"><strong>Total</strong></td>
        <td><strong><?= number_format($total, 2) ?> €</strong></td>
      </tr>
    </tfoot>
  </table>
</div>
