function parseAbilityGroups(AbilityGroups) {
    for (var key in AbilityGroups) {
        var abilityGroupBox = $("#" + AbilityGroups[key].flat_name + "_box");
        abilityGroupBox.find("p.level").text(AbilityGroups[key].base_level);
    }
}

function parseCharacter(character) {
    parseAbilityGroups(character.AbilityGroups);
}

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
    }, parseCharacter, null);
}

$(function() {
    $("#primary_stat_update").click(submitPrimaryStats);

    messageList = [];

    $('#console_handle').click(function() {
        $('#dev_console').hide("slide",
                                 {
                                     direction: 'up',
                                     distance: 200
                                 });
    });

});
