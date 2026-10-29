document.addEventListener('DOMContentLoaded', () => {
  const role = document.querySelector('#role-select');
  const fields = document.querySelector('#merchant-fields');
  const syncRole = () => {
    if (!role || !fields) return;
    const merchant = role.value === 'merchant';
    fields.classList.toggle('hidden', !merchant);
    fields.querySelectorAll('input,textarea').forEach(el => {
      if (['business_name','business_address','document'].includes(el.name)) el.required = merchant;
    });
  };
  role?.addEventListener('change', syncRole); syncRole();
  document.querySelectorAll('[data-step]').forEach(btn => btn.addEventListener('click', () => {
    const input = btn.parentElement.querySelector('input'); if (!input) return;
    const min = Number(input.min || 1), max = Number(input.max || 50), next = Number(input.value || min) + Number(btn.dataset.step);
    input.value = Math.min(max, Math.max(min, next));
  }));
  const method = document.querySelector('#fulfillment-method'); const destination = document.querySelector('#destination-label');
  const syncMethod = () => { if (!method || !destination) return; const delivery = method.value === 'delivery'; destination.classList.toggle('hidden', !delivery); const field=destination.querySelector('textarea'); if(field) field.required=delivery; };
  method?.addEventListener('change', syncMethod); syncMethod();
  document.querySelectorAll('[data-confirm]').forEach(form => form.addEventListener('submit', e => { if (!confirm(form.dataset.confirm)) e.preventDefault(); }));
  const priceForm = document.querySelector('[data-price-preview]')?.closest('form');
  if (priceForm) {
    const preview = priceForm.querySelector('[data-price-preview] strong');
    const price = priceForm.querySelector('[name="normal_price"]'); const discount=priceForm.querySelector('[name="discount_percent"]');
    const fmt = n => new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n);
    const update=()=>{ const p=Number(price?.value||0), d=Number(discount?.value||0); if(preview) preview.textContent=p>0&&d>0?fmt(p*(1-d/100)):'—'; };
    price?.addEventListener('input',update); discount?.addEventListener('input',update); update();
  }
});
