{extends file="layout.tpl"}

{block name=title}Tipuri de modele{/block}

{block name=content}
  {if $showAddForm}
    <h3>Adaugă un tip de model nou</h3>

    Notă: prin această interfață nu se pot crea tipuri de model canonice, ci doar redirectări la alte tipuri.<br/><br/>

    <form method="post" action="tipuri-modele.php">
      <input type="hidden" name="id" value="0"/>
      cod: <input type="text" name="code" value="{$addModelType->code}" size="10" maxlength="10"/><br/>

      cod canonic:
      <select name="canonical">
        {foreach from=$canonicalModelTypes item=mt}
          <option value="{$mt->code}">{$mt->code}</option>
        {/foreach}
      </select><br/>

      descriere: <input type="text" name="description" value="{$addModelType->description}" size="40"/><br/>
      <input type="submit" name="submitAddButton" value="acceptă"/>
      <a href="tipuri-modele">renunță</a>
    </form>
  {/if}

  {if isset($editModelType)}
    <h3>Editează tipul de model {$editModelType->code}</h3>

    <form method="post" action="tipuri-modele.php">
      <input type="hidden" name="id" value="{$editModelType->id}"/>
      cod: {$editModelType->code}<br/>
      {if $editModelType->code != $editModelType->canonical}
        cod canonic: {$editModelType->canonical}<br/>
      {/if}
      descriere: <input type="text" name="description" value="{$editModelType->description}" size="40"/><br/>
      <input type="submit" name="submitEditButton" value="acceptă"/>
      <a href="tipuri-modele">renunță</a>
    </form>
  {/if}

  <h3>Tipuri de modele</h3>

  <table>
    <tr>
      <th>cod</th>
      <th>cod canonic</th>
      <th>descriere</th>
      <th>număr de modele</th>
      <th>număr de lexeme</th>
      <th>acțiuni</th>
    </tr>

    {foreach from=$modelTypes item=mt key=i}
      <tr>
        <td>{$mt->code}</td>
        <td>{if $mt->code != $mt->canonical}{$mt->canonical}{/if}</td>
        <td>{$mt->description}</td>
        <td>{$modelCounts[$i]}</td>
        <td>{$lexemCounts[$i]}</td>
        <td>
          <a href="?editId={$mt->id}">editează</a>
          {if $canDelete[$i]}
            <a href="?deleteId={$mt->id}">șterge</a>
          {/if}
        </td>
      </tr>
    {/foreach}
  </table>

  {if !$showAddForm}
    <a href="?add=1">adaugă un tip de model</a>
  {/if}
{/block}
