document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input.numonly').forEach(function(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            this.classList.toggle('invalid', /\D/.test(this.value));
            this.classList.toggle('valid', /^\d+$/.test(this.value) && this.value !== '');
        });
    });

    document.querySelectorAll('input').forEach(function(input) {
        input.classList.forEach(function(cls) {
            if (cls.startsWith('maxchar-')) {
                let max = parseInt(cls.split('-')[1]);
                input.addEventListener('input', function() {
                    if (this.value.length > max) {
                        this.value = this.value.substring(0, max);
                    }
                    this.classList.toggle('invalid', this.value.length > max);
                    this.classList.toggle('valid', this.value.length <= max);
                });
            }
        });
    });
});