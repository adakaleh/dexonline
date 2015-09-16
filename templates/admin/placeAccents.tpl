{extends file="admin/layout.tpl"}

{block name=title}Plasare accente{/block}

{block name=headerTitle}Plasare accente{/block}

{block name=content}
  Dați clic pe litera care trebuie accentuată sau bifați "Nu
  necesită accent" pentru lexemele care nu necesită accent (cuvinte
  compuse, cuvinte latinești etc.). Lexemele sunt alese la
  întâmplare dintre toate cele neaccentuate. Dacă nu știți ce să
  faceți cu un lexem, săriți-l (nu bifați nimic).

  <form action="placeAccents.php" method="post">
    {foreach from=$lexems item=l}
      {assign var=lexemId value=$l->id}
      {assign var=charArray value=$chars[$lexemId]}
      {assign var=srArray value=$searchResults[$lexemId]}
      <table class="ap_lexemRow">
        <tr>
          <td class="ap_lexemForm">
            <input type="hidden" id="position_{$l->id}"
                   name="position_{$l->id}" value="-1"/>
            {foreach from=$charArray item=char key=cIndex}
              <span class="ap_letter" id="letter_{$l->id}_{$cIndex}" onclick="apSelectLetter({$l->id}, {$cIndex});">{$char}</span>
            {/foreach}
          </td>

          <td>
            <input type="checkbox" id="noAccent_{$l->id}"
                   name="noAccent_{$l->id}" value="X"
                   onclick="apSelectLetter({$l->id}, -1);"
            /><label for="noAccent_{$l->id}">Nu necesită accent</label>
          </td>

          <td>
            <a class="ap_discreetLink" href="#"
               onclick="return toggleDivVisibility('defs_{$l->id}');"
               >Definiții</a>
          </td>
        </tr>
      </table>

      <div id="defs_{$l->id}" style="display: none" class="blDefinitions">
        {foreach from=$srArray item=row}
          {$row->definition->htmlRep}<br/>
          <span class="defDetails">
            Sursa: {$row->source->shortName|escape} |
            {assign var=status value=$row->definition->status}
            {assign var=statusName value=$allStatuses[$status]}
            Starea: {$statusName}
          </span>
          <br/>
        {/foreach}
      </div>
    {/foreach}

    <input type="submit" name="submitButton" value="Trimite"/>
  </form>
{/block}
