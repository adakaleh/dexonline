{extends file="layout.tpl"}

{block name=title}
  {if $src->id}
    Editare sursă {$src->shortName}
  {else}
    Adăugare sursă
  {/if}
{/block}

{block name=content}
  <h3>Editare sursă: {$src->name}</h3>
  <a href="surse">înapoi la lista de surse</a>
  <br/><br/>

  <form method="post" action="editare-sursa">
    <input type="hidden" name="id" value="{$src->id}"/>
    Nume:

    <span class="tooltip2" title="<b>Nume scurt</b> se referă la numele sursei prezentat după fiecare definiție. <b>Nume URL</b> se referă la numele care
                                  apare în URL la căutarea într-o anumită sursă, cum ar fi https://dexonline.ro/definitie-<b>der</b>/copil. Pe ultima linie puteți adăuga o sursă
                                  nouă. Ordonarea surselor este funcțională, dar este greoaie deocamdată. Sursele neoficiale sunt întotdeauna listate după cele oficiale, indiferent
                                  de ordonarea manuală.">&nbsp;</span>

    <input type="text" name="name" value="{$src->name}" size="80"/><br/>
    Nume scurt: <input type="text" name="shortName" value="{$src->shortName}" size="10"/><br/>
    Nume URL: <input type="text" name="urlName" value="{$src->urlName}" size="10"/><br/>
    Autor: <input type="text" name="author" value="{$src->author}" size="55"/><br/>
    Editură: <input type="text" name="publisher" value="{$src->publisher}" size="35"/><br/>
    An: <input type="text" name="year" value="{$src->year}" size="10"/><br/>
    Legătura către formatul scanat: <input type="text" name="link" value="{$src->link}" size="35"/><br/>
    Tip:
    <select name="isOfficial">
      <option value="3" {if $src->isOfficial==3 }selected{/if}>Ascuns</option>
      <option value="2" {if $src->isOfficial==2 }selected{/if}>Oficial</option>
      <option value="1" {if $src->isOfficial==1 }selected{/if}>Specializat</option>
      <option value="0" {if $src->isOfficial==0 }selected{/if}>Neoficial</option>
    </select>
    <br/>

    Număr de definiții (-1 pentru „necunoscut”): <input type="text" name="defCount" value="{$src->defCount}" size="10"/><br/>
    din care digitizate: {$src->ourDefCount}; procent de completare: {$src->percentComplete|string_format:"%.2f"}.

    <br/>

    <input type="checkbox" id="cbIsActive" name="isActive" value="1" {if $src->isActive}checked="checked"{/if}/>
    <label for="cbIsActive">Sursa este activă (și vizibilă tuturor utilizatorilor)</label><br/>

    <input type="checkbox" id="cbCanContribute" name="canContribute" value="1" {if $src->canContribute}checked="checked"{/if}/>
    <label for="cbCanContribute">Deschisă pentru contribuții</label><br/>

    <input type="checkbox" id="cbCanModerate" name="canModerate" value="1" {if $src->canModerate}checked="checked"{/if}/>
    <label for="cbCanModerate">Poate fi aleasă de moderatori</label><br/>

    <input type="checkbox" id="cbCanDistribute" name="canDistribute" value="1" {if $src->canDistribute}checked="checked"{/if}/>
    <label for="cbCanDistribute">Poate fi redistribuită</label><br/>

    <br/>

    <input type="submit" name="submitButton" value="Salvează"/>
    <a href="">renunță</a>
  </form>
{/block}
