<?elements
link lnhash lb "Spustit" confirm "Opravdu spustit migraci?" route "install/migratehash"
block b_hash noprint
?>
<h1>Další akce</h1>

{block b_hash}
<h2>Migrace na bezpečnější hash hesel bcrypt-md5</h2>
<p>Převede hesla hashovaná pomocí md5 na bcrypt-md5. Před spuštěním zálohujte tabulku uživatelů.
Po migraci přepněte v konfiguraci pclib.auth algo na 'bcrypt-md5'.</p>
{lnhash}
{/block}