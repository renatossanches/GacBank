document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.getElementById('cpf');
    if (!cpfInput) return;

    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, ''); // remove tudo que não é número
        if (value.length > 11) value = value.slice(0, 11);

        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

        e.target.value = value;
    });
});
