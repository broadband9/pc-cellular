# Generated by Django 5.1.4 on 2025-01-22 15:08

import django.db.models.deletion
from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('repairs', '0012_repair_site'),
    ]

    operations = [
        migrations.AlterField(
            model_name='location',
            name='site',
            field=models.ForeignKey(blank=True, null=True, on_delete=django.db.models.deletion.CASCADE, related_name='locations', to='repairs.sites'),
        ),
    ]
