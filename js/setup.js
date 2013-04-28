$(function() {
    $("#primary_stat_update").click(submit_primary_stats);

    messageList = [];

});

function submit_primary_stats() {
    ajax_submit({
        'action': 'setMultiplePrimaryStats',
        'object': 'Characterp',
        'Strength': $("input[name='Strength_value']").val(),
        'Perception': $("input[name='Perception_value']").val(),
        'Endurance': $("input[name='Endurance_value']").val(),
        'Charisma': $("input[name='Charisma_value']").val(),
        'Intelligence': $("input[name='Intelligence_value']").val(),
        'Agility': $("input[name='Agility_value']").val(),
        'Luck': $("input[name='Luck_value']").val(),
    });
}
