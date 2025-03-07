<?php $this->extend('template/base'); ?>
<?php $this->section('content'); ?>

<section id="sobre">
    <h3 class="page-title">Sobre o Sistema</h3>
    <fieldset>
        <legend>Dados do E-PREFEITURA</legend>
        <div class="row">
            <div class="col-12">
                <table id="sobreTable" class="cell-border" style="width:100%">
                    <tbody>
                        <tr>
                            <td class="rotulo">Nome</td>
                            <td class="conteudo">E-PREFEITURA - Centro de Gestão e Acesso Digital da Prefeitura dos Campi </td>
                        </tr>
                        <tr>
                            <td class="rotulo">Descrição</td>
                            <td class="conteudo">Disponibiliza o acesso aos sistemas da Prefeitura dos Campi de forma unificada através de um login único.
                                Permite o gerenciamento e controle das permissões de acesso aos sistemas da Prefeitura dos Campi. </td>
                        </tr>
                        <tr>
							<td class="rotulo">Desenvolvedores </td>
							<td class="conteudo"><a href="https://www.github.com/diogosvicente" target="_blank" rel="noopener noreferrer">Diogo Nascimento</a> / <a href="https://www.github.com/LocDog1978" target="_blank" rel="noopener noreferrer">Leandro Carlos</a>
							</td>
						</tr>
                        <tr>
							<td class="rotulo">Site</td>
							<td class="conteudo"><a href="https://www.prefeitura.uerj.br/" target="_blank" rel="noopener noreferrer">Prefeitura dos Campi</a>
							</td>
						</tr>
                        <tr>
							<td class="rotulo">Template</td>
							<td class="conteudo"><a href="https://www.dinfo.uerj.br" target="_blank" rel="noopener noreferrer">DGTI</a> - Diretoria Geral de Tecnologia da Informação
							</td>
						</tr>
                        <tr>
                            <td class="rotulo">Versão corrente</td>
                            <td class="conteudo">v1 </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend>Informações de change log</legend>
        <div class="row">
            <div class="col-12">
            </div>
        </div>
    </fieldset>
</section>

<?php $this->endSection(); ?>