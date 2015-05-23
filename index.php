
  <?php
  $chooseSex = true;
  include '_header.php';
  ?>


	<div class="main">

    <table class="flat-table">
      <tbody>
        <tr>
          <th>Loc General</th>
          <th>Foto</th>
          <th>Nume/Prenume</th>
          <th>Echipă</th>
          <th>Curse</th>
          <th>Punctaj</th>
        </tr>

        <?php $i = 1; foreach (getResults() as $rezultat) : ?>
          <tr align="center">
            <td><?php echo $i++ ?></td>
            <td>
                <?php if ($rezultat['foto']) : ?><img src="<?php echo $rezultat['foto'] ?>" align="center" alt="<?php echo $rezultat['nume'] ?>" ><?php endif ?>
            </td>
            <td><?php echo $rezultat['nume'] ?></td>
            <td><?php echo $rezultat['echipa'] ?></td>
            <td><?php echo $rezultat['curse'] ?></td>
            <td><?php echo $rezultat['puncte'] ?>p</td>
          </tr >
        <?php endforeach ?>

      </tbody>
    </table>

		<div class="comentarii">
			<div align="center" class="fb-comments" data-href="http://clasament.roberthajnal.ro" data-numposts="10" data-colorscheme="light"></div>
		</div>
	</div>

  <?php include '_footer.php'; ?>
