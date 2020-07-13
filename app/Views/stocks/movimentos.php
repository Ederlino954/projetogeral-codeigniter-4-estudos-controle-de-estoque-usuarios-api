<?php 
	$this->extend('layouts/layout_stocks');
?>
<?php $this->section('conteudo') ?>

<div class="row mt-2">
	<div class="col-12">
	
		<div><h5>Movimentos:</h5></div>
		<hr>

		<table class="table table-striped" id="tabela_movimentos">
			<thead class="thead-dark">
				<th>ID Produto</th>		
				<th>Designacao</th>	
				<th>Quantidade</th>	
				<th>Entrada / saída</th>	
				<th>Data Movimento</th>	
				<th>Observações</th>	
			<tbody>			
				<?php foreach($movimentos as $movimennto): ?>
					<tr>
						<td><?php echo $movimennto['id_produto'] ?></td>
						<td><?php echo $movimennto['designacao'] ?></td>
						<td><?php echo $movimennto['quantidade'] ?></td>
						<td><?php echo $movimennto['entrada_saida'] ?></td>
						<td><?php echo $movimennto['data_movimento'] ?></td>
						<td><?php echo $movimennto['observacoes'] ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
			

	</div>
</div>

<script>
$(document).ready( function () {
    $('#tabela_movimentos').DataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json"			
        }
	});
});
</script>


<?php $this->endSection() ?>