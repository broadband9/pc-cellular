# Generated by Django 5.1.4 on 2025-01-11 17:58

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('repairs', '0007_repair_button_function_ok_and_more'),
    ]

    operations = [
        migrations.AddField(
            model_name='repair',
            name='hinge_damage',
            field=models.BooleanField(default=None, null=True),
        ),
    ]
