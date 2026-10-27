// Sistema SAEP - Scripts auxiliares

document.addEventListener('DOMContentLoaded', function () {
    // Confirmacao de exclusao
    const botoesExcluir = document.querySelectorAll('.btn-excluir');
    botoesExcluir.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Tem certeza que deseja excluir este registro?')) {
                e.preventDefault();
            }
        });
    });
});
