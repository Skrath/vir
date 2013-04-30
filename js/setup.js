$(function() {
    $("#primary_stat_update").click(submitPrimaryStats);

    messageList = [];

});

function submitPrimaryStats() {
    ajaxSubmit({
        'action': 'setMultiplePrimaryStats',
        'object': 'Character',
        'Strength': $("input[name='Strength_value']").val(),
        'Perception': $("input[name='Perception_value']").val(),
        'Endurance': $("input[name='Endurance_value']").val(),
        'Charisma': $("input[name='Charisma_value']").val(),
        'Intelligence': $("input[name='Intelligence_value']").val(),
        'Agility': $("input[name='Agility_value']").val(),
        'Luck': $("input[name='Luck_value']").val(),
    });
}
