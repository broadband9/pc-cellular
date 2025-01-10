from email.policy import default
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
    repair_number = models.CharField(max_length=100, default="")
    customer = models.ForeignKey('customers.Customer', on_delete=models.CASCADE)
    device_type = models.CharField(max_length=50)
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
