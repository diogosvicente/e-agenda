$(document).ready(function () {
    let espacoCount = 1; // Contador de espaços

    // Adicionar novo espaço
    $("#addEspaco").click(function () {
        espacoCount++;
        let newEspaco = `
            <div class="espaco-entry" data-espaco="${espacoCount}">
                <fieldset class="fieldset-child">
                    <legend class="fieldset-child">Espaço ${espacoCount}</legend>

                    <div class="row">
                        <div class="col-sm-12">
                            <label for="espaco_${espacoCount}" class="form-label">Espaço Disponível: *</label>
                            <select name="espaco[]" class="form-control espaco-select" required>
                                <option value="">Selecione um espaço</option>
                                <?php foreach ($campus as $campusItem): ?>
                                    <optgroup label="<?php echo $campusItem->nome; ?>">
                                        <?php foreach ($campusItem->predios as $predio): ?>
                                            <optgroup label="➝ <?php echo $predio->nome; ?>">
                                                <?php foreach ($predio->espacos as $espaco): ?>
                                                    <option value="<?php echo $espaco->id; ?>">
                                                        <?php echo $espaco->nome . " (Capacidade: " . $espaco->capacidade . ")"; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="datas_horarios">
                        <div class="row mt-3 data-hora-entry">
                            <div class="col-sm-4">
                                <label for="data_inicio[]" class="form-label">Data Início:</label>
                                <input type="date" name="data_inicio[]" class="form-control" required>
                            </div>
                            <div class="col-sm-4">
                                <label for="hora_inicio[]" class="form-label">Hora de Início:</label>
                                <input type="time" name="hora_inicio[]" class="form-control" required>
                            </div>
                            <div class="col-sm-3">
                                <label for="hora_fim[]" class="form-label">Hora de Fim:</label>
                                <input type="time" name="hora_fim[]" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-sm-6">
                            <button type="button" class="btn btn-success addDataHora">+ Adicionar Data/Hora</button>
                        </div>
                        <div class="col-sm-6 text-end">
                            <button type="button" class="btn btn-danger removeEspaco">Remover Espaço</button>
                        </div>
                    </div>
                </fieldset>
            </div>
        `;

        $("#espacos-container").append(newEspaco);
    });

    // Adicionar nova Data/Hora no respectivo espaço
    $(document).on("click", ".addDataHora", function () {
        let espaco = $(this).closest(".espaco-entry");
        let newEntry = `
            <div class="row mt-3 data-hora-entry">
                <div class="col-sm-4">
                    <input type="date" name="data_inicio[]" class="form-control" required>
                </div>
                <div class="col-sm-4">
                    <input type="time" name="hora_inicio[]" class="form-control" required>
                </div>
                <div class="col-sm-3">
                    <input type="time" name="hora_fim[]" class="form-control" required>
                </div>
                <div class="col-sm-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-entry">X</button>
                </div>
            </div>
        `;
        espaco.find(".datas_horarios").append(newEntry);
    });

    // Remover Data/Hora (exceto a primeira de cada espaço)
    $(document).on("click", ".remove-entry", function () {
        let espaco = $(this).closest(".espaco-entry");
        if (espaco.find(".data-hora-entry").length > 1) {
            $(this).closest(".data-hora-entry").remove();
        }
    });

    // Remover Espaço e atualizar contador
    $(document).on("click", ".removeEspaco", function () {
        $(this).closest(".espaco-entry").remove();
        espacoCount--; // Decrementa a contagem de espaços
    });
});

$('.nav.nav-tabs .nav-item a').click(function (e) {
    e.preventDefault();
    $('.nav.nav-tabs .nav-item a').removeClass('active').removeAttr('aria-current');
    $(this).addClass('active').attr('aria-current', 'page');
    let tabId = $(this).attr('href');
    $(tabId).addClass('show active').siblings().removeClass('show active');
});