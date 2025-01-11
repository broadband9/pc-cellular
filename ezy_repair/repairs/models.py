from email.policy import default
from random import choices

from django.db import models
from django.contrib.auth.models import User  # Import the User model


class RepairStatus(models.Model):
    name = models.CharField(max_length=50)
    description = models.CharField(max_length=1000, default="")

    def __str__(self):
        return self.name


class Location(models.Model):
    name = models.CharField(max_length=100)
    address = models.CharField(max_length=1000, default="")

    def __str__(self):
        return self.name


class Make(models.Model):
    name = models.CharField(max_length=100)

    def __str__(self):
        return self.name


class ActivityLog(models.Model):
    description = models.TextField()
    user = models.ForeignKey(
        User,
        on_delete=models.SET_NULL,  # Set a default user if the associated user is deleted
        null=True
    )
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.description} - {self.created_at}"


class Repair(models.Model):
    Device_Type_Choices = [
        ('Mobile', 'Mobile'),
        ('Tablet', 'Tablet'),
        ('Laptop', 'Laptop'),
    ]
    repair_number = models.CharField(max_length=100, default="")
    customer = models.ForeignKey('customers.Customer', on_delete=models.CASCADE)
    device_type = models.CharField(choices=Device_Type_Choices, max_length=50)
    imei = models.CharField(max_length=500, null=True, blank=True)
    network = models.CharField(max_length=100, null=True, blank=True)
    storage = models.CharField(max_length=100, null=True, blank=True)
    ram = models.CharField(max_length=100, null=True, blank=True)
    operating_system = models.CharField(max_length=100, null=True, blank=True)

    tampered = models.BooleanField(default=None, null=True)
    missing_part = models.BooleanField(default=None, null=True)
    power_up = models.BooleanField(default=None, null=True)
    liquid_damage = models.BooleanField(default=None, null=True)

    screen_damage = models.BooleanField(default=None, null=True)
    hinge_damage = models.BooleanField(default=None, null=True)
    keyboard_functional = models.BooleanField(default=None, null=True)
    trackpad_functional = models.BooleanField(default=None, null=True)

    risk_lcd = models.BooleanField(default=None, null=True)
    sim_removed = models.BooleanField(default=None, null=True)
    button_function_ok = models.BooleanField(default=None, null=True)
    risk_biometric = models.BooleanField(default=None, null=True)
    risk_back = models.BooleanField(default=None, null=True)
    camera_lens_back_damage = models.BooleanField(default=None, null=True)
    lens_lcd_damage = models.BooleanField(default=None, null=True)

    signature = models.ImageField(upload_to='signatures/', null=True, blank=True)  # New field for signature
    status = models.ForeignKey(RepairStatus, on_delete=models.SET_NULL, null=True, blank=True)
    location = models.ForeignKey(Location, on_delete=models.SET_NULL, null=True, blank=True)
    make = models.ForeignKey(Make, on_delete=models.SET_NULL, null=True, blank=True)
    model = models.CharField(max_length=100)
    issue_description = models.TextField()
    passcode = models.CharField(default="", max_length=20)
    estimated_cost = models.DecimalField(max_digits=10, decimal_places=2)
    finalized_price = models.DecimalField(max_digits=10, decimal_places=2, null=True, blank=True)
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"Repair #{self.pk} - {self.device_type}"
