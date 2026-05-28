package com.mysticnusa.app.ui.components

import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.OutlinedTextFieldDefaults
import androidx.compose.material3.Text
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import com.mysticnusa.app.ui.theme.MysticGold
import com.mysticnusa.app.ui.theme.MysticPurple
import com.mysticnusa.app.ui.theme.TextSecondary

@Composable
fun MysticTextField(
    value: String,
    onValueChange: (String) -> Unit,
    label: String,
    modifier: Modifier = Modifier,
    isPassword: Boolean = false
) {
    OutlinedTextField(
        value = value,
        onValueChange = onValueChange,
        label = { Text(label) },
        modifier = modifier.fillMaxWidth(),
        shape = RoundedCornerShape(12.dp),
        colors = OutlinedTextFieldDefaults.colors(
            focusedBorderColor = MysticGold,
            unfocusedBorderColor = MysticPurple,
            focusedLabelColor = MysticGold,
            unfocusedLabelColor = TextSecondary,
            cursorColor = MysticGold
        ),
        singleLine = true
    )
}
