document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formEditUser');
    const role = document.getElementById('rol_id');
    const orgBox = document.getElementById('boxOrganismo');
    const org = document.getElementById('organismo_id');
    const leagueBox = document.getElementById('boxLiga');
    const teamBox = document.getElementById('boxEquipo');
    const league = document.getElementById('liga_id');
    const team = document.getElementById('equipo_id');

    const roleKey = () => role?.selectedOptions[0]?.dataset.roleKey || '';

    const filterOptions = () => {
        const key = roleKey();
        const orgId = org?.value || '';
        if (orgBox) orgBox.classList.toggle('d-none', !['ORGANISMO', 'LIGA', 'EQUIPO'].includes(key));
        if (leagueBox) leagueBox.classList.toggle('d-none', !['LIGA', 'EQUIPO'].includes(key));
        if (teamBox) teamBox.classList.toggle('d-none', key !== 'EQUIPO');
        if (org && org.tagName === 'SELECT') org.required = ['ORGANISMO', 'LIGA', 'EQUIPO'].includes(key);
        if (league) league.required = ['LIGA', 'EQUIPO'].includes(key);
        if (team) team.required = key === 'EQUIPO';

        if (league) {
            Array.from(league.options).forEach(option => {
                if (!option.value) return;
                option.hidden = !!orgId && option.dataset.organismo !== orgId;
            });
            if (league.selectedOptions[0]?.hidden) league.value = '';
        }
        if (team) {
            Array.from(team.options).forEach(option => {
                if (!option.value) return;
                option.hidden = !!league?.value && option.dataset.liga !== league.value;
            });
            if (team.selectedOptions[0]?.hidden) team.value = '';
        }
    };

    role?.addEventListener('change', filterOptions);
    org?.addEventListener('change', () => {
        if (league) league.value = '';
        if (team) team.value = '';
        filterOptions();
    });
    league?.addEventListener('change', () => {
        if (team) team.value = '';
        filterOptions();
    });
    filterOptions();

    form?.addEventListener('submit', event => {
        if (!form.reportValidity()) event.preventDefault();
    });
});